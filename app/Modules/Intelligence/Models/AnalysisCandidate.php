<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisCandidate extends Model
{
    /**
     * Rule yang cocok pada satu analisis, sudah berurutan sesuai ranking engine.
     */
    protected $table = 'intelligence_analysis_candidates';

    protected $fillable = [
        'id_analysis',
        'id_rule',
        'id_rule_version',
        'id_diagnosis',
        'code',
        'severity',
        'priority',
        'specificity',
        'rank_position',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'specificity' => 'integer',
            'rank_position' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class, 'id_analysis');
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'id_rule');
    }

    public function ruleVersion(): BelongsTo
    {
        return $this->belongsTo(RuleVersion::class, 'id_rule_version');
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'id_diagnosis');
    }
}
