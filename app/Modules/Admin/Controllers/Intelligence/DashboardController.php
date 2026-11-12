<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Services\Intelligence\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboard) {}

    public function __invoke(): View
    {
        return view('admin.intelligence.dashboard', [
            'summary' => $this->dashboard->summary(),
        ]);
    }
}
