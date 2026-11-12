<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Services\Intelligence\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(private readonly AuditLogService $auditLogs) {}

    public function index(Request $request): View
    {
        return view('admin.intelligence.audit.index', [
            'logs' => $this->auditLogs->paginate($request->only(['search', 'entity_type'])),
            'filters' => $request->only(['search', 'entity_type']),
        ]);
    }
}
