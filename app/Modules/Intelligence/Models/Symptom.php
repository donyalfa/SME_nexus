<?php

namespace App\Modules\Intelligence\Models;

use App\Modules\Intelligence\Domain\Enums\RuleOperator;
use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Symptom extends Model
{
    protected $table = 'autopsy_symptoms';

    protected $fillable = [
        'code',
        'name',
        'description',
        'metric_key',
        'operator',
        'threshold',
        'severity',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'operator' => RuleOperator::class,
            'severity' => Severity::class,
            'threshold' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class, 'id_symptom');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id_user');
    }

    public function conditionLabel(): string
    {
        return sprintf(
            '%s %s %s',
            $this->metric_key,
            $this->operator->symbol(),
            rtrim(rtrim((string) $this->threshold, '0'), '.')
        );
    }
}
