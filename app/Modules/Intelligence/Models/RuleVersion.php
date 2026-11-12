<?php

namespace App\Modules\Intelligence\Models;

use App\Modules\Intelligence\Domain\Enums\RuleVersionStatus;
use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class RuleVersion extends Model
{
    protected $table = 'autopsy_rule_versions';

    protected $fillable = [
        'id_rule',
        'id_diagnosis',
        'version',
        'status',
        'is_active',
        'severity',
        'priority',
        'notes',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => RuleVersionStatus::class,
            'is_active' => 'boolean',
            'published_at' => 'datetime',
            'version' => 'integer',
            'id_diagnosis' => 'integer',
            'severity' => Severity::class,
            'priority' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', RuleVersionStatus::Published->value);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'id_rule');
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'id_diagnosis');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(RuleGroup::class, 'id_rule_version')->orderBy('group_order');
    }

    public function conditions(): HasManyThrough
    {
        return $this->hasManyThrough(
            RuleCondition::class,
            RuleGroup::class,
            'id_rule_version',
            'id_rule_group',
            'id',
            'id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function specificity(): int
    {
        return $this->conditions()->count();
    }
}
