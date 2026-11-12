<?php

namespace App\Modules\Intelligence\Models;

use App\Shared\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recommendation extends Model
{
    protected $table = 'autopsy_recommendations';

    protected $fillable = [
        'id_diagnosis',
        'code',
        'title',
        'description',
        'priority',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Langkah action plan, tersimpan satu baris per langkah.
     */
    public function steps(): HasMany
    {
        return $this->hasMany(RecommendationStep::class, 'id_recommendation')
            ->orderBy('step_order')
            ->orderBy('id');
    }

    /**
     * Daftar langkah sebagai array agar tampilan dan form lama tetap bekerja.
     */
    protected function actionSteps(): Attribute
    {
        return Attribute::get(fn (): array => $this->steps->pluck('step')->all());
    }

    /**
     * Ganti seluruh langkah action plan dengan daftar baru.
     *
     * @param  array<int, mixed>  $steps
     */
    public function syncSteps(array $steps): void
    {
        $this->steps()->delete();

        $rows = [];

        foreach (array_values($steps) as $index => $step) {
            if (! is_string($step) || trim($step) === '') {
                continue;
            }

            $rows[] = [
                'step_order' => $index + 1,
                'step' => mb_substr(trim($step), 0, 1000),
            ];
        }

        if ($rows !== []) {
            $this->steps()->createMany($rows);
        }

        $this->unsetRelation('steps');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class, 'id_diagnosis');
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
