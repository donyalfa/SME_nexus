<?php

namespace App\Modules\Decision\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Decision\Models\ActionPlan;
use App\Modules\Intelligence\Models\Analysis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ActionPlanController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $userId = $request->user()->id_user;

        $data = $request->validate([
            'id_analysis' => ['required', 'integer', 'exists:intelligence_analyses,id'],
            'id_finding' => ['nullable', 'integer', 'exists:intelligence_analysis_findings,id'],
            'task' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $analysis = Analysis::query()
            ->whereKey($data['id_analysis'])
            ->where('id_user', $userId)
            ->firstOrFail();

        $this->ensureFindingBelongsToAnalysis($analysis, $data['id_finding'] ?? null);

        $analysis->actionPlans()->create([
            'id_user' => $userId,
            'id_finding' => $data['id_finding'] ?? null,
            'assigned_to' => $userId,
            'task' => $data['task'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'status' => ActionPlan::STATUS_TODO,
        ]);

        return back()->with('success', 'Action Plan berhasil dibuat.');
    }

    public function storeFromStep(Request $request): RedirectResponse
    {
        $userId = $request->user()->id_user;

        $data = $request->validate([
            'id_analysis' => ['required', 'integer', 'exists:intelligence_analyses,id'],
            'id_finding' => ['required', 'integer', 'exists:intelligence_analysis_findings,id'],
            'id_step' => ['required', 'integer', 'exists:intelligence_analysis_finding_steps,id'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $analysis = Analysis::query()
            ->whereKey($data['id_analysis'])
            ->where('id_user', $userId)
            ->firstOrFail();

        $finding = $analysis->findings()
            ->with('steps')
            ->whereKey($data['id_finding'])
            ->firstOrFail();

        if ($finding->type !== 'recommendation') {
            throw ValidationException::withMessages([
                'id_finding' => 'Action Plan hanya dapat dibuat dari langkah rekomendasi.',
            ]);
        }

        $step = $finding->steps->firstWhere('id', (int) $data['id_step']);

        if (! $step) {
            throw ValidationException::withMessages([
                'id_step' => 'Langkah rekomendasi tidak sesuai dengan temuan analisis.',
            ]);
        }

        $task = trim($step->step);
        $dueDate = $data['due_date'] ?? null;

        // Cari Action Plan yang sama agar tidak membuat duplikat.
        $existingPlan = ActionPlan::query()
            ->where('id_user', $userId)
            ->where('id_analysis', $analysis->id)
            ->where('id_finding', $finding->id)
            ->where('task', $task)
            ->first();

        if ($existingPlan) {
            // Deadline baru memperbarui deadline yang sudah ada.
            // Jika form kosong, deadline lama tetap dipertahankan.
            if ($dueDate !== null && $dueDate !== '') {
                $existingPlan->update([
                    'due_date' => $dueDate,
                ]);

                return back()->with(
                    'success',
                    'Action Plan sudah ada. Deadline berhasil diperbarui.'
                );
            }

            return back()->with(
                'warning',
                'Langkah tersebut sudah ada di Action Plan. Tidak ada perubahan deadline.'
            );
        }

        // Jika belum ada, buat Action Plan baru.
        // Deadline tetap opsional.
        $analysis->actionPlans()->create([
            'id_user' => $userId,
            'id_finding' => $finding->id,
            'assigned_to' => $userId,
            'task' => $task,
            'description' => $finding->description,
            'due_date' => $dueDate,
            'status' => ActionPlan::STATUS_TODO,
        ]);

        return back()->with(
            'success',
            'Langkah rekomendasi berhasil ditambahkan ke Action Plan.'
        );
    }

    public function update(Request $request, ActionPlan $actionPlan): RedirectResponse
    {
        $userId = $request->user()->id_user;

        $actionPlan = ActionPlan::query()
            ->whereKey($actionPlan->getKey())
            ->where('id_user', $userId)
            ->firstOrFail();

        $data = $request->validate([
            'id_finding' => ['nullable', 'integer', 'exists:intelligence_analysis_findings,id'],
            'task' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $analysis = Analysis::query()
            ->whereKey($actionPlan->id_analysis)
            ->where('id_user', $userId)
            ->firstOrFail();

        $this->ensureFindingBelongsToAnalysis($analysis, $data['id_finding'] ?? null);
        $actionPlan->update($data);

        return back()->with('success', 'Action Plan berhasil diperbarui.');
    }

    public function updateStatus(Request $request, ActionPlan $actionPlan): RedirectResponse
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    ActionPlan::STATUS_TODO,
                    ActionPlan::STATUS_IN_PROGRESS,
                    ActionPlan::STATUS_DONE,
                ]),
            ],
        ]);

        $userId = $request->user()->id_user;
        $requestedStatus = $validated['status'];

        DB::transaction(function () use ($actionPlan, $userId, $requestedStatus): void {
            $plan = ActionPlan::query()
                ->whereKey($actionPlan->getKey())
                ->where('id_user', $userId)
                ->lockForUpdate()
                ->firstOrFail();

            $allowedNextStatus = [
                ActionPlan::STATUS_TODO => ActionPlan::STATUS_IN_PROGRESS,
                ActionPlan::STATUS_IN_PROGRESS => ActionPlan::STATUS_DONE,
                ActionPlan::STATUS_DONE => ActionPlan::STATUS_IN_PROGRESS,
            ];

            if (($allowedNextStatus[$plan->status] ?? null) !== $requestedStatus) {
                throw ValidationException::withMessages([
                    'status' => 'Transisi status Action Plan tidak diperbolehkan.',
                ]);
            }

            $plan->status = $requestedStatus;
            $plan->completed_at = $requestedStatus === ActionPlan::STATUS_DONE ? now() : null;
            $plan->save();
        });

        return back()->with('success', 'Status Action Plan berhasil diperbarui.');
    }

    private function ensureFindingBelongsToAnalysis(Analysis $analysis, mixed $findingId): void
    {
        if ($findingId === null) {
            return;
        }

        if (! $analysis->findings()->whereKey($findingId)->exists()) {
            throw ValidationException::withMessages([
                'id_finding' => 'Diagnosis/rekomendasi tidak berasal dari analisis yang dipilih.',
            ]);
        }
    }
}
