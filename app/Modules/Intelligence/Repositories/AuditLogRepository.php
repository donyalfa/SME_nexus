<?php

namespace App\Modules\Intelligence\Repositories;

use App\Modules\Intelligence\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogRepository
{
    public function recent(int $limit = 5)
    {
        return AuditLog::query()
            ->with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return AuditLog::query()
            ->with('user')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $escaped = addcslashes($search, '%_\\');
                $query->where(function ($query) use ($escaped) {
                    $query->where('action', 'like', "%{$escaped}%")
                        ->orWhere('entity_type', 'like', "%{$escaped}%");
                });
            })
            ->when($filters['entity_type'] ?? null, fn ($query, $type) => $query->where('entity_type', $type))
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
