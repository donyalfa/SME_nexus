<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalysisFinding extends Model
{
    /**
     * Satu baris diagnosis atau rekomendasi hasil analisis Business Autopsy.
     */
    protected $table = 'intelligence_analysis_findings';

    public const TYPE_DIAGNOSIS = 'diagnosis';

    public const TYPE_RECOMMENDATION = 'recommendation';

    public const SOURCE_RULE = 'rule';

    public const SOURCE_FALLBACK = 'fallback';

    protected $fillable = [
        'id_analysis',
        'type',
        'source',
        'ref_id',
        'id_rule_version',
        'code',
        'title',
        'description',
        'severity',
        'priority',
        'priority_label',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Langkah action plan yang disalin dari rekomendasi master.
     */
    public function steps(): HasMany
    {
        return $this->hasMany(AnalysisFindingStep::class, 'id_finding')
            ->orderBy('step_order')
            ->orderBy('id');
    }

    /** Action Plan yang merujuk diagnosis/rekomendasi ini. */
    public function actionPlans(): HasMany
    {
        return $this->hasMany(ActionPlan::class, 'id_finding');
    }

    protected function actionSteps(): Attribute
    {
        return Attribute::get(fn (): array => $this->steps->pluck('step')->all());
    }

    public function scopeDiagnoses(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_DIAGNOSIS);
    }

    public function scopeRecommendations(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_RECOMMENDATION);
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class, 'id_analysis');
    }

    public function ruleVersion(): BelongsTo
    {
        return $this->belongsTo(RuleVersion::class, 'id_rule_version');
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'ref_id');
    }

    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(Recommendation::class, 'ref_id');
    }
}
