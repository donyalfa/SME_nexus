<?php

namespace App\Modules\Intelligence\Models;

use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    protected $table = 'autopsy_diagnoses';

    protected $fillable = [
        'code',
        'name',
        'description',
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

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class, 'id_diagnosis');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'id_diagnosis');
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
