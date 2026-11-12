<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Modules\Intelligence\Domain\Enums\RuleOperator;
use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\StoreSymptomRequest;
use App\Modules\Admin\Requests\Intelligence\UpdateSymptomRequest;
use App\Modules\Intelligence\Models\Symptom;
use App\Modules\Intelligence\Services\SymptomService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SymptomController extends Controller
{
    public function __construct(private readonly SymptomService $symptoms) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);

        if (($filters['is_active'] ?? '') === '') {
            $filters['is_active'] = null;
        }

        return view('admin.intelligence.symptoms.index', [
            'symptoms' => $this->symptoms->paginate($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.intelligence.symptoms.form', [
            'symptom' => new Symptom([
                'operator' => RuleOperator::LessThan,
                'severity' => Severity::Medium,
                'is_active' => true,
            ]),
            'operators' => RuleOperator::cases(),
            'severities' => Severity::cases(),
        ]);
    }

    public function store(StoreSymptomRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->symptoms->create($data);

        return redirect()
            ->route('admin.intelligence.symptoms.index')
            ->with('success', 'Gejala berhasil ditambahkan.');
    }

    public function edit(Symptom $symptom): View
    {
        return view('admin.intelligence.symptoms.form', [
            'symptom' => $symptom,
            'operators' => RuleOperator::cases(),
            'severities' => Severity::cases(),
        ]);
    }

    public function update(UpdateSymptomRequest $request, Symptom $symptom): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->symptoms->update($symptom, $data);

        return redirect()
            ->route('admin.intelligence.symptoms.index')
            ->with('success', 'Gejala berhasil diperbarui.');
    }

    public function deactivate(Symptom $symptom): RedirectResponse
    {
        $this->symptoms->deactivate($symptom);

        return back()->with('success', 'Gejala berhasil dinonaktifkan.');
    }

    public function activate(Symptom $symptom): RedirectResponse
    {
        $this->symptoms->activate($symptom);

        return back()->with('success', 'Gejala berhasil diaktifkan.');
    }
}
