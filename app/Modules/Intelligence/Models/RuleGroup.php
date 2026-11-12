<?php

namespace App\Modules\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RuleGroup extends Model
{
    protected $table = 'autopsy_rule_groups';

    protected $fillable = [
        'id_rule_version',
        'group_order',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'group_order' => 'integer',
        ];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(RuleVersion::class, 'id_rule_version');
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class, 'id_rule_group')->orderBy('id');
    }
}
