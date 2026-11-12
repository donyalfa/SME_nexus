<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleCondition extends Model
{
    protected $table = 'autopsy_rule_conditions';

    protected $fillable = [
        'id_rule_group',
        'id_symptom',
        'is_negated',
    ];

    protected function casts(): array
    {
        return [
            'is_negated' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(RuleGroup::class, 'id_rule_group');
    }

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class, 'id_symptom');
    }
}
