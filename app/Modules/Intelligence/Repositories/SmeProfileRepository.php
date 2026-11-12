<?php

namespace App\Modules\Intelligence\Repositories;

use App\Modules\Intelligence\Models\SmeProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SmeProfileRepository
{
    /**
     * Mengambil daftar profil UMKM dari tabel sme_profiles.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return SmeProfile::query()
            ->with(['user', 'category'])
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $escaped = addcslashes($search, '%_\\');

                $query->where(function ($query) use ($escaped) {
                    $query->where('business_name', 'like', "%{$escaped}%")
                        ->orWhere('city', 'like', "%{$escaped}%");
                });
            })
            ->when(
                $filters['id_category'] ?? null,
                fn ($query, $category) => $query->where('id_category', $category)
            )
            ->when(
                $filters['id_user'] ?? null,
                fn ($query, $user) => $query->where('id_user', $user)
            )
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): SmeProfile
    {
        return SmeProfile::query()
            ->with(['user', 'category'])
            ->findOrFail($id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): SmeProfile
    {
        return SmeProfile::query()->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(SmeProfile $profile, array $data): SmeProfile
    {
        $profile->update($data);

        return $profile->refresh();
    }

    public function delete(SmeProfile $profile): void
    {
        $profile->delete();
    }

    public function hasBusinessData(SmeProfile $profile): bool
    {
        return $profile->businessData()->exists();
    }
}
