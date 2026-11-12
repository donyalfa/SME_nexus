<?php

namespace App\Modules\Intelligence\Models;

namespace App\Modules\Intelligence\Models;

use App\Modules\Decision\Models\ActionPlan;
use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\RuleVersion;
use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
class Analysis extends Model
{
    /**
     * Tabel penyimpanan hasil analisis Business Autopsy.
     */
    protected $table = 'intelligence_analyses';

    protected $fillable = [
        'id_user',
        'period_prev_date',
        'period_current_date',
        'omzet_prev',
        'omzet_current',
        'customer_prev',
        'customer_current',
        'sales_prev',
        'sales_current',
        'product_count',
        'avg_price',
        'marketing_spend',
        'operational_cost',
        'omzet_change',
        'customer_change',
        'sales_change',
        'status',
        'primary_diagnosis_id',
        'primary_rule_version_id',
    ];

    protected function casts(): array
    {
        return [
            'period_prev_date' => 'date',
            'period_current_date' => 'date',
            'omzet_prev' => 'decimal:2',
            'omzet_current' => 'decimal:2',
            'customer_prev' => 'decimal:2',
            'customer_current' => 'decimal:2',
            'sales_prev' => 'decimal:2',
            'sales_current' => 'decimal:2',
            'product_count' => 'decimal:2',
            'avg_price' => 'decimal:2',
            'marketing_spend' => 'decimal:2',
            'operational_cost' => 'decimal:2',
            'omzet_change' => 'decimal:2',
            'customer_change' => 'decimal:2',
            'sales_change' => 'decimal:2',
        ];
    }

    /**
     * Diagnosis dan rekomendasi hasil analisis, tersimpan satu baris per temuan.
     */
    public function findings(): HasMany
    {
        return $this->hasMany(AnalysisFinding::class, 'id_analysis')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /** Action Plan yang dibuat berdasarkan hasil analisis ini. */
    public function actionPlans(): HasMany
    {
        return $this->hasMany(ActionPlan::class, 'id_analysis');
    }

    /**
     * Gejala yang terdeteksi saat analisis, satu baris per gejala.
     */
    public function symptoms(): HasMany
    {
        return $this->hasMany(AnalysisSymptom::class, 'id_analysis');
    }

    /**
     * Kandidat rule hasil ranking engine, satu baris per rule yang cocok.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(AnalysisCandidate::class, 'id_analysis')
            ->orderBy('rank_position')
            ->orderBy('id');
    }

    public function diagnosisFindings(): HasMany
    {
        return $this->findings()->where('type', AnalysisFinding::TYPE_DIAGNOSIS);
    }

    public function recommendationFindings(): HasMany
    {
        return $this->findings()->where('type', AnalysisFinding::TYPE_RECOMMENDATION);
    }

    /**
     * Bentuk daftar diagnosis untuk ditampilkan (kompatibel dengan view lama).
     */
    protected function diagnoses(): Attribute
    {
        return Attribute::get(fn (): array => $this->findingItems(AnalysisFinding::TYPE_DIAGNOSIS)
            ->map(fn (AnalysisFinding $finding): array => [
                'title' => $finding->title,
                'severity' => $finding->severity,
                'description' => $finding->description,
            ])
            ->values()
            ->all());
    }

    /**
     * Bentuk daftar rekomendasi untuk ditampilkan (kompatibel dengan view lama).
     */
    protected function recommendations(): Attribute
    {
        return Attribute::get(fn (): array => $this->findingItems(AnalysisFinding::TYPE_RECOMMENDATION)
            ->map(fn (AnalysisFinding $finding): array => [
                'title' => $finding->title,
                'description' => $finding->description,
                'priority' => $finding->priority_label ?? self::priorityLabel($finding->priority),
            ])
            ->values()
            ->all());
    }

    private function findingItems(string $type): Collection
    {
        return $this->findings
            ->where('type', $type)
            ->sortBy('sort_order')
            ->values();
    }

    private static function priorityLabel(?int $priority): string
    {
        return match (true) {
            $priority === null => 'low',
            $priority <= 1 => 'high',
            $priority === 2 => 'medium',
            default => 'low',
        };
    }

    public function primaryDiagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'primary_diagnosis_id');
    }

    public function primaryRuleVersion(): BelongsTo
    {
        return $this->belongsTo(RuleVersion::class, 'primary_rule_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Label ringkas rentang periode analisis.
     */
    public function periodLabel(): ?string
    {
        if (! $this->period_prev_date && ! $this->period_current_date) {
            return null;
        }

        return $this->period_prev_date?->format('d M Y') . ' → ' . $this->period_current_date?->format('d M Y');
    }
}
