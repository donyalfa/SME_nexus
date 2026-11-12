<?php

namespace App\Modules\Admin\Repositories;

use App\Shared\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $escaped = addcslashes($search, '%_\\');
                $query->where(function ($query) use ($escaped) {
                    $query->where('full_name', 'like', "%{$escaped}%")
                        ->orWhere('email', 'like', "%{$escaped}%");
                });
            })
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->where('role', $role))
            ->when(array_key_exists('is_verified', $filters) && $filters['is_verified'] !== null, function ($query) use ($filters) {
                $bool = filter_var($filters['is_verified'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                if ($bool !== null) {
                    $query->where('is_verified', $bool);
                } else {
                    $query->where('is_verified', (bool) $filters['is_verified']);
                }
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): User
    {
        return User::query()->findOrFail($id);
    }

    public function owners()
    {
        return User::query()
            ->where('role', 'sme_owner')
            ->orderBy('full_name')
            ->get();
    }

    public function ownersForSelection(?int $selectedId = null)
    {
        return User::query()
            ->where(function ($query) use ($selectedId) {
                $query->where('role', 'sme_owner');

                if ($selectedId !== null) {
                    $query->orWhere('id_user', $selectedId);
                }
            })
            ->orderBy('full_name')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function hasProfiles(User $user): bool
    {
        return $user->smeProfiles()->exists()
            || $user->businessProfiles()->exists()
            || $user->intelligenceAnalyses()->exists();
    }
}
