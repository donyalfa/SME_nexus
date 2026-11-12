<?php

namespace App\Modules\Admin\Services\Intelligence;

use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Models\Symptom;
use App\Modules\Intelligence\Models\SmeProfile;
use App\Shared\Models\User;

class AdminDashboardService
{
    public function __construct(private readonly AuditLogService $auditLogs) {}

    /**
     * @return array<string, int|mixed>
     */
    public function summary(): array
    {
        return [
            'users' => User::query()->count(),
            'owners' => User::query()->where('role', 'sme_owner')->count(),
            'profiles' => SmeProfile::query()->count(),
            'symptoms' => Symptom::query()->active()->count(),
            'diagnoses' => Diagnosis::query()->active()->count(),
            'rules' => Rule::query()->active()->count(),
            'recommendations' => Recommendation::query()->active()->count(),
            'recent_logs' => $this->auditLogs->recent(6),
        ];
    }
}
