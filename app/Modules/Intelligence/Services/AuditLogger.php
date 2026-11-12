<?php

namespace App\Modules\Intelligence\Services;

use App\Modules\Intelligence\Models\AuditLog;
use App\Shared\Models\SystemLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Catat perubahan penting ke audit log modul dan system_logs legacy.
     *
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        $user = Auth::user();
        $userId = $user?->getKey();
        $ipAddress = request()?->ip();
        $userAgent = request()?->userAgent();

        AuditLog::create([
            'id_user' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues === [] ? null : $oldValues,
            'new_values' => $newValues === [] ? null : $newValues,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);

        if ($userId !== null) {
            SystemLog::create([
                'id_user' => $userId,
                'action_type' => $action,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'created_at' => now(),
            ]);
        }
    }
}
