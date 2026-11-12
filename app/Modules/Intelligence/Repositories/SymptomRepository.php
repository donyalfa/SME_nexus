<?php

namespace App\Modules\Intelligence\Repositories;

use App\Modules\Intelligence\Models\Symptom;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SymptomRepository
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Symptom::query()
            ->withCount('conditions')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $escaped = addcslashes($search, '%_\\');
                $query->where(function ($query) use ($escaped) {
                    $query->where('code', 'like', "%{$escaped}%")
                        ->orWhere('name', 'like', "%{$escaped}%")
                        ->orWhere('metric_key', 'like', "%{$escaped}%");
                });
            })
            ->when(array_key_exists('is_active', $filters) && $filters['is_active'] !== null, function ($query) use ($filters) {
                $bool = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if ($bool !== null) {
                    $query->where('is_active', $bool);
                } else {
                    $query->where('is_active', (bool) $filters['is_active']);
                }
            })
            ->when($filters['id_symptom'] ?? null, fn ($query, $id) => $query->whereKey($id))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function allActive()
    {
        return Symptom::query()->active()->orderBy('name')->get();
    }

    public function all()
    {
        return Symptom::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
    }

    public function find(int $id): Symptom
    {
        return Symptom::query()->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Symptom
    {
        return Symptom::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Symptom $symptom, array $data): Symptom
    {
        $symptom->update($data);

        return $symptom->refresh();
    }

    public function deactivate(Symptom $symptom): Symptom
    {
        $symptom->update(['is_active' => false]);

        return $symptom->refresh();
    }

    public function isUsed(Symptom $symptom): bool
    {
        return $symptom->conditions()->exists();
    }
}
