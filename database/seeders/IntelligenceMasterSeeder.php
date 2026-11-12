<?php

namespace Database\Seeders;

use App\Modules\Intelligence\Domain\Enums\RuleOperator;
use App\Modules\Intelligence\Domain\Enums\RuleVersionStatus;
use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Modules\Intelligence\Models\BusinessCategory;
use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Models\Symptom;
use App\Modules\Intelligence\Repositories\RuleRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntelligenceMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedCategories();

            $symptoms = $this->seedDeclineSymptoms();

            $this->seedDeclineRules($symptoms);

            $this->seedFinancialRules();
        });
    }

    private function seedCategories(): void
    {
        foreach ([
            ['category_name' => 'Kuliner', 'deskripsi' => 'Usaha makanan dan minuman'],
            ['category_name' => 'Retail', 'deskripsi' => 'Perdagangan barang eceran'],
            ['category_name' => 'Jasa', 'deskripsi' => 'Usaha jasa dan konsultasi'],
            ['category_name' => 'Manufaktur', 'deskripsi' => 'Produksi dan pengolahan barang'],
            ['category_name' => 'Pertanian', 'deskripsi' => 'Pertanian, perkebunan, dan agribisnis'],
            ['category_name' => 'Teknologi', 'deskripsi' => 'Produk digital dan teknologi'],
        ] as $category) {
            BusinessCategory::query()->updateOrCreate(
                ['category_name' => $category['category_name']],
                ['deskripsi' => $category['deskripsi']]
            );
        }
    }

    private function seedDeclineSymptoms(): array
    {
        $metrics = [
            'revenue' => ['name' => 'Omzet', 'metric' => 'revenue_change_percent'],
            'customer' => ['name' => 'Jumlah pelanggan', 'metric' => 'customer_change_percent'],
            'sales' => ['name' => 'Volume penjualan', 'metric' => 'sales_change_percent'],
        ];

        $levels = [
            'light' => ['label' => 'Ringan', 'operator' => RuleOperator::LessThanOrEqual, 'threshold' => -1, 'severity' => Severity::Low],
            'medium' => ['label' => 'Sedang', 'operator' => RuleOperator::LessThan, 'threshold' => -5, 'severity' => Severity::Medium],
            'significant' => ['label' => 'Signifikan', 'operator' => RuleOperator::LessThan, 'threshold' => -10, 'severity' => Severity::High],
            'crash' => ['label' => 'Anjlok', 'operator' => RuleOperator::LessThan, 'threshold' => -25, 'severity' => Severity::Critical],
        ];

        $result = [];

        foreach ($metrics as $metricCode => $metric) {
            foreach ($levels as $levelCode => $level) {
                $symptom = Symptom::query()->updateOrCreate(
                    ['code' => "{$metricCode}_decline_{$levelCode}"],
                    [
                        'name' => "{$metric['name']} Menurun {$level['label']}",
                        'description' => "{$metric['name']} mengalami penurunan tingkat {$level['label']}.",
                        'metric_key' => $metric['metric'],
                        'operator' => $level['operator']->value,
                        'threshold' => $level['threshold'],
                        'severity' => $level['severity']->value,
                        'is_active' => true,
                    ]
                );

                $result[$metricCode][$levelCode] = $symptom->getKey();
            }
        }

        return $result;
    }

    private function seedDeclineRules(array $symptoms): void
    {
        $repository = app(RuleRepository::class);

        $metrics = [
            'revenue' => 'omzet',
            'customer' => 'pelanggan',
            'sales' => 'volume_penjualan',
        ];

        $levels = [
            'light' => [
                'label' => 'Ringan',
                'severity' => Severity::Low,
                'priority' => 4,
                'steps' => [
                    'Bandingkan hasil usaha dengan periode sebelumnya dan catat sumber penurunan.',
                    'Periksa produk atau layanan yang mengalami penurunan kontribusi.',
                    'Buat target perbaikan untuk periode berikutnya.',
                    'Evaluasi hasil perbaikan setiap minggu.',
                ],
            ],
            'medium' => [
                'label' => 'Sedang',
                'severity' => Severity::Medium,
                'priority' => 3,
                'steps' => [
                    'Identifikasi produk, layanan, atau kanal penjualan yang paling banyak mengalami penurunan.',
                    'Hubungi pelanggan yang mulai jarang bertransaksi dan cari tahu penyebabnya.',
                    'Uji penawaran bundling atau promosi dengan margin yang sehat.',
                    'Tetapkan target pemulihan dan pantau hasilnya setiap minggu.',
                ],
            ],
            'significant' => [
                'label' => 'Signifikan',
                'severity' => Severity::High,
                'priority' => 2,
                'steps' => [
                    'Analisis penyebab penurunan berdasarkan produk, pelanggan, dan kanal penjualan.',
                    'Prioritaskan produk atau layanan dengan permintaan dan margin terbaik.',
                    'Hubungi pelanggan lama dan jalankan program pemulihan pelanggan.',
                    'Susun program promosi terukur dengan batas biaya yang jelas.',
                    'Evaluasi pencapaian target setiap minggu.',
                ],
            ],
            'crash' => [
                'label' => 'Anjlok',
                'severity' => Severity::Critical,
                'priority' => 1,
                'steps' => [
                    'Periksa penyebab penurunan dan dampaknya terhadap arus kas usaha.',
                    'Identifikasi sumber pendapatan yang masih bertahan dan prioritaskan pemulihannya.',
                    'Hubungi pelanggan utama dan pelanggan tidak aktif untuk mencari penyebab kehilangan transaksi.',
                    'Hentikan sementara pengeluaran atau pemasaran yang tidak menghasilkan kontribusi.',
                    'Buat rencana pemulihan 30 hari dengan target mingguan.',
                    'Evaluasi hasil pemulihan setiap minggu dan sesuaikan strategi.',
                ],
            ],
        ];

        foreach ($metrics as $metricCode => $metricName) {
            foreach ($levels as $levelCode => $level) {
                $code = "{$metricCode}_decline_{$levelCode}";

                $diagnosis = Diagnosis::query()->firstOrCreate(
                    ['code' => $code],
                    [
                        'name' => ucfirst(str_replace('_', ' ', $metricName)) . " Menurun {$level['label']}",
                        'description' => "Diagnosis penurunan {$metricName} tingkat {$level['label']}.",
                        'severity' => $level['severity']->value,
                        'priority' => $level['priority'],
                        'is_active' => true,
                    ]
                );

                $rule = Rule::query()->firstOrCreate(
                    ['code' => "rule_{$code}"],
                    [
                        'name' => "Rule {$diagnosis->name}",
                        'description' => "Mendeteksi penurunan {$metricName} tingkat {$level['label']}.",
                        'id_diagnosis' => $diagnosis->getKey(),
                        'severity' => $level['severity']->value,
                        'priority' => $level['priority'],
                        'is_active' => true,
                    ]
                );

                $groups = $this->declineGroups($symptoms[$metricCode], $levelCode);

                $this->publishRuleIfMissing($repository, $rule, $groups);

                $this->seedRecommendation(
                    $diagnosis,
                    "rec_{$code}",
                    "Strategi Pemulihan {$metricName} {$level['label']}",
                    "Langkah tindakan untuk mengatasi penurunan {$metricName}.",
                    $level['priority'],
                    $level['steps']
                );
            }
        }
    }

    private function declineGroups(array $symptoms, string $level): array
    {
        $conditions = match ($level) {
            'light' => [
                ['id_symptom' => $symptoms['light']],
                ['id_symptom' => $symptoms['medium'], 'is_negated' => true],
            ],
            'medium' => [
                ['id_symptom' => $symptoms['medium']],
                ['id_symptom' => $symptoms['significant'], 'is_negated' => true],
            ],
            'significant' => [
                ['id_symptom' => $symptoms['significant']],
                ['id_symptom' => $symptoms['crash'], 'is_negated' => true],
            ],
            'crash' => [
                ['id_symptom' => $symptoms['crash']],
            ],
            default => [],
        };

        return [[
            'label' => "Tingkat penurunan {$level}",
            'conditions' => $conditions,
        ]];
    }

    private function seedFinancialRules(): void
    {
        $repository = app(RuleRepository::class);

        $definitions = [
            [
                'code' => 'high_operating_cost',
                'name' => 'Biaya Operasional Tinggi',
                'description' => 'Rasio biaya operasional terhadap omzet melebihi 70%.',
                'metric' => 'operating_expense_ratio',
                'operator' => RuleOperator::GreaterThan,
                'threshold' => 70,
                'severity' => Severity::High,
                'priority' => 2,
                'steps' => [
                    'Rinci seluruh biaya operasional berdasarkan kategori pengeluaran.',
                    'Identifikasi biaya yang meningkat dan tidak memberikan kontribusi langsung.',
                    'Evaluasi harga pemasok dan negosiasikan biaya pembelian.',
                    'Tetapkan batas anggaran operasional dan evaluasi setiap minggu.',
                ],
            ],
            [
                'code' => 'low_profit_margin',
                'name' => 'Margin Laba Rendah',
                'description' => 'Margin laba usaha berada di bawah 10%.',
                'metric' => 'profit_margin',
                'operator' => RuleOperator::LessThan,
                'threshold' => 10,
                'severity' => Severity::High,
                'priority' => 2,
                'steps' => [
                    'Hitung biaya pokok dan keuntungan setiap produk atau layanan.',
                    'Identifikasi produk dengan margin paling rendah.',
                    'Evaluasi harga jual dan biaya bahan atau pemasok.',
                    'Prioritaskan penjualan produk dengan margin sehat.',
                    'Tetapkan batas diskon agar transaksi tidak merugikan usaha.',
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            $symptom = Symptom::query()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'metric_key' => $definition['metric'],
                    'operator' => $definition['operator']->value,
                    'threshold' => $definition['threshold'],
                    'severity' => $definition['severity']->value,
                    'is_active' => true,
                ]
            );

            $diagnosis = Diagnosis::query()->firstOrCreate(
                ['code' => $definition['code']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'severity' => $definition['severity']->value,
                    'priority' => $definition['priority'],
                    'is_active' => true,
                ]
            );

            $rule = Rule::query()->firstOrCreate(
                ['code' => 'rule_' . $definition['code']],
                [
                    'name' => 'Rule ' . $definition['name'],
                    'description' => $definition['description'],
                    'id_diagnosis' => $diagnosis->getKey(),
                    'severity' => $definition['severity']->value,
                    'priority' => $definition['priority'],
                    'is_active' => true,
                ]
            );

            $this->publishRuleIfMissing($repository, $rule, [[
                'label' => 'Kondisi keuangan',
                'conditions' => [['id_symptom' => $symptom->getKey()]],
            ]]);

            $this->seedRecommendation(
                $diagnosis,
                'rec_' . $definition['code'],
                'Strategi ' . $definition['name'],
                'Langkah perbaikan untuk menjaga efisiensi dan profitabilitas usaha.',
                $definition['priority'],
                $definition['steps']
            );
        }
    }

    private function publishRuleIfMissing(
        RuleRepository $repository,
        Rule $rule,
        array $groups
    ): void {
        $hasPublished = $rule->versions()
            ->where('status', RuleVersionStatus::Published->value)
            ->exists();

        if ($hasPublished) {
            return;
        }

        $version = $repository->createVersion(
            $rule,
            [
                'version' => ((int) $rule->versions()->max('version')) + 1,
                'status' => RuleVersionStatus::Draft->value,
                'is_active' => false,
                'notes' => 'Versi awal master rule Intelligence.',
            ],
            $groups
        );

        $repository->publishVersion($version);
    }

    private function seedRecommendation(
        Diagnosis $diagnosis,
        string $code,
        string $title,
        string $description,
        int $priority,
        array $steps
    ): void {
        $recommendation = Recommendation::query()->firstOrCreate(
            ['code' => $code],
            [
                'id_diagnosis' => $diagnosis->getKey(),
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'is_active' => true,
            ]
        );

        if (! $recommendation->steps()->exists()) {
            $recommendation->syncSteps($steps);
        }
    }
}
