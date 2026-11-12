<?php

namespace App\Modules\Decision\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Decision\Models\ActionPlan;
use App\Modules\Intelligence\Models\Analysis;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DecisionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id_user;
        $ownerQuery = Analysis::query()->where('id_user', $userId);

        $analyses = (clone $ownerQuery)
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $latestAnalysis = (clone $ownerQuery)
            ->with(['findings.steps'])
            ->orderByDesc('id')
            ->first();

        $stats = [
            'total' => (clone $ownerQuery)->count(),
            'needs_attention' => (clone $ownerQuery)
                ->whereIn('status', ['critical', 'warning'])
                ->count(),
            'latest' => $latestAnalysis,
        ];

        $actionPlans = ActionPlan::query()
            ->where('id_user', $userId)
            ->with(['finding', 'assignee'])
            ->orderByRaw("CASE status
                WHEN 'todo' THEN 1
                WHEN 'in_progress' THEN 2
                WHEN 'done' THEN 3
                ELSE 4
            END")
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->paginate(8, ['*'], 'plans_page')
            ->withQueryString();

        $statusCounts = ActionPlan::query()
            ->where('id_user', $userId)
            ->selectRaw('status, COUNT(*) AS aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $actionPlanStats = [
            'total' => (int) $statusCounts->sum(),
            'todo' => (int) ($statusCounts[ActionPlan::STATUS_TODO] ?? 0),
            'in_progress' => (int) ($statusCounts[ActionPlan::STATUS_IN_PROGRESS] ?? 0),
            'done' => (int) ($statusCounts[ActionPlan::STATUS_DONE] ?? 0),
        ];

        $actionPlanFindings = $latestAnalysis?->findings ?? collect();

        return view('modules.decision.index', compact(
            'analyses',
            'stats',
            'actionPlans',
            'actionPlanStats',
            'actionPlanFindings'
        ));
    }
}
