<?php

namespace App\Modules\Admin\Services\Intelligence;

use App\Modules\Intelligence\Repositories\AuditLogRepository;

class AuditLogService
{
    public function __construct(private readonly AuditLogRepository $auditLogs) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->auditLogs->paginate($filters);
    }

    public function recent(int $limit = 5)
    {
        return $this->auditLogs->recent($limit);
    }
}
