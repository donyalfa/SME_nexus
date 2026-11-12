<?php

namespace App\Modules\Intelligence\Controllers;

use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Http\Controllers\Controller;
use App\Modules\Intelligence\Models\Analysis;
use App\Modules\Intelligence\Models\AnalysisFinding;
use App\Modules\Intelligence\Services\DiagnosisEvaluationService;
use App\Modules\Intelligence\Services\IntelligenceMetricMapper;
use Illuminate\Http\Request;

class AutopsyController extends Controller
{
    public function __construct(
        private readonly DiagnosisEvaluationService $evaluation,
        private readonly IntelligenceMetricMapper $metricMapper,
    ) {}

    private const TIER_STABLE = 0;
    private const TIER_RINGAN = 1;
    private const TIER_SEDANG = 2;
    private const TIER_SIGNIFIKAN = 3;
    private const TIER_ANJLOK = 4;

    private static function declineTier(float $change): int
    {
        if ($change > -1) {
            return self::TIER_STABLE;
        }
        if ($change >= -5) {
            return self::TIER_RINGAN;
        }
        if ($change >= -10) {
            return self::TIER_SEDANG;
        }
        if ($change >= -25) {
            return self::TIER_SIGNIFIKAN;
        }

        return self::TIER_ANJLOK;
    }

    private static function tierSeverity(int $tier): string
    {
        return match ($tier) {
            self::TIER_RINGAN => 'low',
            self::TIER_SEDANG => 'medium',
            default => 'high',
        };
    }

    private static function tierLabel(int $tier): string
    {
        return match ($tier) {
            self::TIER_RINGAN => 'Ringan',
            self::TIER_SEDANG => 'Sedang',
            self::TIER_SIGNIFIKAN => 'Signifikan',
            self::TIER_ANJLOK => 'Anjlok',
            default => '',
        };
    }

    public function index()
    {
        return view('umkm.intelligence.autopsy');
    }

    public function show(Request $request, Analysis $analysis)
    {
        // Hasil analisis hanya boleh dibuka oleh pemiliknya.
        abort_unless(
            $analysis->id_user === $request->user()->id_user,
            404,
            'Hasil analisis tidak ditemukan.'
        );

        $analysis->loadMissing('findings');

        return view('umkm.intelligence.analysis', ['analysis' => $analysis]);
    }

    public function analyze(Request $request)
    {
        $data = $request->validate([
            'omzet_prev' => 'required|numeric|gt:0|max:999999999999999',
            'omzet_current' => 'required|numeric|min:0|max:999999999999999',
            'customer_prev' => 'required|numeric|gt:0|max:999999999999999',
            'customer_current' => 'required|numeric|min:0|max:999999999999999',
            'sales_prev' => 'required|numeric|gt:0|max:999999999999999',
            'sales_current' => 'required|numeric|min:0|max:999999999999999',
            'product_count' => 'required|numeric|min:0|max:9999999',
            'avg_price' => 'required|numeric|min:0|max:999999999999999',
            'marketing_spend' => 'required|numeric|min:0|max:999999999999999',
            'operational_cost' => 'required|numeric|min:0|max:999999999999999',
            'period_prev_date' => 'nullable|date|before_or_equal:today',
            'period_current_date' => 'nullable|date|before_or_equal:today',
        ]);

        $analysis = $this->storeAnalysis($request->user()->id_user, $data);

        return redirect()->route('intelligence.autopsy.show', $analysis);
    }

    /**
     * Simpan snapshot analisis ke database.
     */
    private function storeAnalysis(int $id_user, array $data): Analysis
    {
        $result = self::compute($data);

        $status = $result['status'];

        $evaluation = $this->evaluation->evaluate($this->metricMapper->fromAutopsyInput($data));

        $primaryDiagnosisId = null;
        $primaryRuleVersionId = null;
        $detectedSymptoms = $evaluation->detectedSymptoms->map(fn ($symptom) => [
            'id_symptom' => $symptom->getKey(),
            'code' => $symptom->code,
            'name' => $symptom->name,
            'metric_key' => $symptom->metric_key,
        ])->values()->all();
        $candidates = collect($evaluation->matchedCandidates)->values()->map(function (array $candidate, int $index): array {
            $severity = $candidate['severity'] ?? null;

            return [
                'id_rule' => $candidate['rule_id'] ?? null,
                'id_rule_version' => ($candidate['version'] ?? null)?->getKey(),
                'id_diagnosis' => ($candidate['diagnosis'] ?? null)?->getKey(),
                'code' => mb_substr((string) ($candidate['code'] ?? ''), 0, 100),
                'severity' => $severity instanceof Severity ? $severity->value : (is_string($severity) ? $severity : null),
                'priority' => $candidate['priority'] ?? null,
                'specificity' => $candidate['specificity'] ?? null,
                'rank_position' => $index + 1,
                'is_primary' => $index === 0,
            ];
        })->all();

        $findings = [];

        if ($evaluation->hasMatch()) {
            $primary = $evaluation->primaryDiagnosis();
            $primaryDiagnosisId = $primary?->getKey();
            $primaryRuleVersionId = $evaluation->primaryVersion()?->getKey();

            $severity = $primary?->severity?->value ?? 'medium';
            $status = match ($severity) {
                'critical' => 'critical',
                'high' => 'critical',
                'medium' => 'warning',
                'low' => 'monitor',
                default => $result['status'],
            };

            $findings[] = [
                'type' => AnalysisFinding::TYPE_DIAGNOSIS,
                'source' => AnalysisFinding::SOURCE_RULE,
                'ref_id' => $primaryDiagnosisId,
                'id_rule_version' => $primaryRuleVersionId,
                'code' => $primary?->code,
                'title' => mb_substr((string) $primary?->name, 0, 150),
                'description' => $primary?->description,
                'severity' => $severity,
                'priority' => $primary?->priority,
                'priority_label' => null,
                'sort_order' => 0,
            ];

            foreach ($evaluation->recommendations->values() as $index => $recommendation) {
                $findings[] = [
                    'type' => AnalysisFinding::TYPE_RECOMMENDATION,
                    'source' => AnalysisFinding::SOURCE_RULE,
                    'ref_id' => $recommendation->getKey(),
                    'id_rule_version' => $primaryRuleVersionId,
                    'code' => $recommendation->code,
                    'title' => mb_substr((string) $recommendation->title, 0, 150),
                    'description' => $recommendation->description,
                    'priority' => $recommendation->priority,
                    'priority_label' => self::priorityLabel($recommendation->priority),
                    'steps' => $recommendation->steps->values()->map(fn ($step, $stepIndex) => [
                        'step_order' => $stepIndex + 1,
                        'step' => $step->step,
                    ])->all(),
                    'sort_order' => $index,
                ];
            }
        } else {
            foreach ($result['diagnoses'] as $index => $diagnosis) {
                $findings[] = [
                    'type' => AnalysisFinding::TYPE_DIAGNOSIS,
                    'source' => AnalysisFinding::SOURCE_FALLBACK,
                    'title' => mb_substr((string) $diagnosis['title'], 0, 150),
                    'description' => $diagnosis['description'] ?? null,
                    'severity' => $diagnosis['severity'] ?? null,
                    'sort_order' => $index,
                ];
            }

            foreach ($result['recommendations'] as $index => $recommendation) {
                $priority = $recommendation['priority'] ?? null;

                $findings[] = [
                    'type' => AnalysisFinding::TYPE_RECOMMENDATION,
                    'source' => AnalysisFinding::SOURCE_FALLBACK,
                    'title' => mb_substr((string) $recommendation['title'], 0, 150),
                    'description' => $recommendation['description'] ?? null,
                    'priority' => is_numeric($priority) ? (int) $priority : null,
                    'priority_label' => is_string($priority) && ! is_numeric($priority) ? $priority : null,
                    'sort_order' => $index,
                ];
            }
        }

        $analysis = Analysis::create([
            'id_user' => $id_user,
            'period_prev_date' => $data['period_prev_date'] ?? null,
            'period_current_date' => $data['period_current_date'] ?? null,
            'omzet_prev' => $data['omzet_prev'],
            'omzet_current' => $data['omzet_current'],
            'customer_prev' => $data['customer_prev'],
            'customer_current' => $data['customer_current'],
            'sales_prev' => $data['sales_prev'],
            'sales_current' => $data['sales_current'],
            'product_count' => $data['product_count'],
            'avg_price' => $data['avg_price'],
            'marketing_spend' => $data['marketing_spend'],
            'operational_cost' => $data['operational_cost'],
            'omzet_change' => $result['omzetChange'],
            'customer_change' => $result['customerChange'],
            'sales_change' => $result['salesChange'],
            'status' => $status,
            'primary_diagnosis_id' => $primaryDiagnosisId,
            'primary_rule_version_id' => $primaryRuleVersionId,
        ]);

        $analysis->symptoms()->createMany($detectedSymptoms);
        $analysis->candidates()->createMany($candidates);

        foreach ($findings as $finding) {
            $steps = $finding['steps'] ?? [];
            unset($finding['steps']);

            $analysis->findings()->create($finding)->steps()->createMany($steps);
        }

        return $analysis;
    }

    /**
     * Label prioritas untuk ditampilkan dari prioritas numerik master data.
     */
    private static function priorityLabel(?int $priority): ?string
    {
        return match (true) {
            $priority === null => null,
            $priority <= 1 => 'high',
            $priority === 2 => 'medium',
            default => 'low',
        };
    }

    /**
     * Hitung hasil analisis dari data yang sudah tervalidasi.
     *
     * @return array{omzetChange: float, customerChange: float, salesChange: float, status: string, diagnoses: list<array<string, mixed>>, recommendations: list<array<string, mixed>>}
     */
    private static function compute(array $data): array
    {
        $omzetChange = (($data['omzet_current'] - $data['omzet_prev']) / $data['omzet_prev']) * 100;
        $customerChange = (($data['customer_current'] - $data['customer_prev']) / $data['customer_prev']) * 100;
        $salesChange = (($data['sales_current'] - $data['sales_prev']) / $data['sales_prev']) * 100;

        $omzetTier = self::declineTier($omzetChange);
        $customerTier = self::declineTier($customerChange);
        $salesTier = self::declineTier($salesChange);

        $diagnoses = [];
        $recommendations = [];

        // Setiap indikator dicek mandiri: mulai dari turun -1% sudah ada kondisi.
        // Makin parah turunnya, makin parah tier-nya (Ringan -> Sedang -> Signifikan -> Anjlok).
        if ($omzetTier > self::TIER_STABLE) {
            $label = self::tierLabel($omzetTier);
            $diagnoses[] = [
                'title' => "Omzet Menurun {$label}",
                'severity' => self::tierSeverity($omzetTier),
                'description' => 'Omzet menurun ' . abs(round($omzetChange, 1)) . '% dari periode sebelumnya. '
                    . ($omzetTier === self::TIER_ANJLOK ? 'Penurunan ini tergolong anjlok dan memerlukan evaluasi segera.' : ''),
            ];
            $recommendations[] = $omzetTier === self::TIER_RINGAN
                ? [
                    'title' => 'Pantau Penurunan Omzet',
                    'description' => 'Penurunan omzet masih ringan. Pantau tren beberapa hari ke depan dan pastikan tidak berlanjut.',
                    'priority' => 'low',
                ]
                : [
                    'title' => 'Evaluasi Penurunan Omzet',
                    'description' => 'Tinjau kanal penjualan dan strategi harga yang paling berdampak, lalu terapkan perbaikan bertahap.',
                    'priority' => self::tierSeverity($omzetTier),
                ];
        }

        if ($customerTier > self::TIER_STABLE) {
            $label = self::tierLabel($customerTier);
            $diagnoses[] = [
                'title' => "Jumlah Pelanggan Menurun {$label}",
                'severity' => self::tierSeverity($customerTier),
                'description' => 'Jumlah pelanggan menurun ' . abs(round($customerChange, 1)) . '%.'
                    . ($customerTier === self::TIER_ANJLOK ? ' Anjloknya jumlah pelanggan mengindikasikan masalah akuisisi atau retensi yang serius.' : ''),
            ];
            $recommendations[] = $customerTier === self::TIER_RINGAN
                ? [
                    'title' => 'Pantau Jumlah Pelanggan',
                    'description' => 'Penurunan pelanggan masih ringan. Pantau kampanye berjalan dan pastikan akuisisi tetap terjaga.',
                    'priority' => 'low',
                ]
                : [
                    'title' => 'Tingkatkan Akuisisi Pelanggan',
                    'description' => 'Fokuskan pada strategi marketing untuk menarik pelanggan baru dan perbaiki retensi pelanggan lama.',
                    'priority' => self::tierSeverity($customerTier),
                ];
        }

        if ($salesTier > self::TIER_STABLE) {
            $label = self::tierLabel($salesTier);
            $diagnoses[] = [
                'title' => "Volume Penjualan Menurun {$label}",
                'severity' => self::tierSeverity($salesTier),
                'description' => 'Volume penjualan menurun ' . abs(round($salesChange, 1)) . '%.'
                    . ($salesTier === self::TIER_ANJLOK ? ' Anjloknya volume penjualan memerlukan tindakan segera.' : ''),
            ];
            $recommendations[] = $salesTier === self::TIER_RINGAN
                ? [
                    'title' => 'Pantau Penjualan',
                    'description' => 'Penurunan penjualan masih ringan. Pantau stok, harga, dan trafik toko untuk mencegah memburuk.',
                    'priority' => 'low',
                ]
                : [
                    'title' => 'Tingkatkan Retensi Pelanggan',
                    'description' => 'Buat program loyalitas atau penawaran khusus untuk pelanggan existing.',
                    'priority' => self::tierSeverity($salesTier),
                ];
        }

        // Eskalasi gabungan: kalau ketiga indikator kompak turun, naikkan parah satu tingkat.
        $allDeclining = $omzetTier > self::TIER_STABLE
            && $customerTier > self::TIER_STABLE
            && $salesTier > self::TIER_STABLE;

        if ($allDeclining) {
            $worstTier = max($omzetTier, $customerTier, $salesTier);
            $escalatedTier = min($worstTier + 1, self::TIER_ANJLOK);
            $diagnoses[] = [
                'title' => 'Semua Indikator Turun Bersama',
                'severity' => self::tierSeverity($escalatedTier),
                'description' => 'Omzet, customer, dan penjualan turun bersamaan. Pola ini mengindikasikan masalah bisnis yang lebih luas, bukan fluktuasi satu indikator.',
            ];
            if ($escalatedTier >= self::TIER_SEDANG) {
                $recommendations[] = [
                    'title' => 'Evaluasi Menyeluruh',
                    'description' => 'Lakukan evaluasi menyeluruh atas produk, harga, marketing, dan operasional karena penurunan terjadi di semua indikator.',
                    'priority' => self::tierSeverity($escalatedTier),
                ];
            }
        }

        if ($data['marketing_spend'] > 0 && $data['omzet_current'] > 0) {
            $marketingEfficiency = $data['omzet_current'] / $data['marketing_spend'];
            if ($marketingEfficiency < 3) {
                $diagnoses[] = [
                    'title' => 'Efisiensi Marketing Rendah',
                    'severity' => 'medium',
                    'description' => 'Rasio omzet terhadap biaya marketing hanya ' . round($marketingEfficiency, 1) . 'x.',
                ];
                $recommendations[] = [
                    'title' => 'Optimalkan Strategi Marketing',
                    'description' => 'Evaluasi kembali channel marketing yang fokus pada yang memberikan ROI terbaik.',
                    'priority' => 'medium',
                ];
            }
        }

        if ($data['operational_cost'] > 0 && $data['omzet_current'] > 0) {
            $operationalRatio = ($data['operational_cost'] / $data['omzet_current']) * 100;
            if ($operationalRatio > 70) {
                $diagnoses[] = [
                    'title' => 'Biaya Operasional Tinggi',
                    'severity' => 'high',
                    'description' => 'Biaya operasional mencapai ' . round($operationalRatio, 1) . '% dari omzet.',
                ];
                $recommendations[] = [
                    'title' => 'Kurangi Biaya Operasional',
                    'description' => 'Identifikasi area pengurangan biaya seperti negosiasi ulang dengan supplier.',
                    'priority' => 'high',
                ];
            }
        }

        $allStable = $omzetTier === self::TIER_STABLE
            && $customerTier === self::TIER_STABLE
            && $salesTier === self::TIER_STABLE;

        if ($allStable) {
            $diagnoses[] = [
                'title' => 'Bisnis Dalam Kondisi Baik',
                'severity' => 'low',
                'description' => 'Tidak ada penurunan yang terdeteksi. Kondisi bisnis Anda masih tergolong baik.',
            ];
            $recommendations[] = [
                'title' => 'Pertahankan Performa',
                'description' => 'Teruskan strategi yang sudah baik dan pantau secara berkala.',
                'priority' => 'low',
            ];
        }

        $hasHigh = collect($diagnoses)->contains('severity', 'high');
        $hasMedium = collect($diagnoses)->contains('severity', 'medium');
        $status = $hasHigh ? 'critical' : ($hasMedium ? 'warning' : ($allStable ? 'ok' : 'monitor'));

        return [
            'omzetChange' => $omzetChange,
            'customerChange' => $customerChange,
            'salesChange' => $salesChange,
            'status' => $status,
            'diagnoses' => $diagnoses,
            'recommendations' => $recommendations,
        ];
    }
}
