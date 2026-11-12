<?php

namespace App\Modules\Intelligence\Models;

use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rule extends Model
{
    protected $table = 'autopsy_rules';

    protected $fillable = [
        'code',
        'name',
        'description',
        'id_diagnosis',
        'severity',
        'priority',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'severity' => Severity::class,
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'id_diagnosis');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(RuleVersion::class, 'id_rule')->orderByDesc('version');
    }

    public function activeVersion(): HasOne
    {
        return $this->hasOne(RuleVersion::class, 'id_rule')->where('is_active', true);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id_user');
    }
}
