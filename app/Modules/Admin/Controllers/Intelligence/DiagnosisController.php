<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\StoreDiagnosisRequest;
use App\Modules\Admin\Requests\Intelligence\UpdateDiagnosisRequest;
use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Services\DiagnosisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function __construct(private readonly DiagnosisService $diagnoses) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);

        if (($filters['is_active'] ?? '') === '') {
            $filters['is_active'] = null;
        }

        return view('admin.intelligence.diagnoses.index', [
            'diagnoses' => $this->diagnoses->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.intelligence.diagnoses.form', [
            'diagnosis' => new Diagnosis([
                'severity' => Severity::Medium,
                'priority' => 100,
                'is_active' => true,
            ]),
            'severities' => Severity::cases(),
        ]);
    }

    public function store(StoreDiagnosisRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->diagnoses->create($data);

        return redirect()
            ->route('admin.intelligence.diagnoses.index')
            ->with('success', 'Master diagnosis berhasil ditambahkan.');
    }

    public function edit(Diagnosis $diagnosis): View
    {
        return view('admin.intelligence.diagnoses.form', [
            'diagnosis' => $diagnosis,
            'severities' => Severity::cases(),
        ]);
    }

    public function update(UpdateDiagnosisRequest $request, Diagnosis $diagnosis): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->diagnoses->update($diagnosis, $data);

        return redirect()
            ->route('admin.intelligence.diagnoses.index')
            ->with('success', 'Master diagnosis berhasil diperbarui.');
    }

    public function deactivate(Diagnosis $diagnosis): RedirectResponse
    {
        $this->diagnoses->deactivate($diagnosis);

        return back()->with('success', 'Master diagnosis berhasil dinonaktifkan.');
    }

    public function activate(Diagnosis $diagnosis): RedirectResponse
    {
        $this->diagnoses->activate($diagnosis);

        return back()->with('success', 'Master diagnosis berhasil diaktifkan.');
    }
}
