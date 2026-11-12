<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\StoreRuleRequest;
use App\Modules\Admin\Requests\Intelligence\UpdateRuleRequest;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Services\DiagnosisService;
use App\Modules\Intelligence\Services\RuleService;
use App\Modules\Intelligence\Services\SymptomService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RuleController extends Controller
{
    public function __construct(
        private readonly RuleService $rules,
        private readonly DiagnosisService $diagnoses,
        private readonly SymptomService $symptoms,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'id_diagnosis', 'is_active']);

        if (($filters['is_active'] ?? '') === '') {
            $filters['is_active'] = null;
        }

        return view('admin.intelligence.rules.index', [
            'rules' => $this->rules->paginate($filters),
            'filters' => $filters,
            'diagnoses' => $this->diagnoses->allActive(),
        ]);
    }

    public function create(): View
    {
        return view('admin.intelligence.rules.form', [
            'rule' => new Rule(['severity' => 'medium', 'priority' => 100, 'is_active' => false]),
            'version' => null,
            'diagnoses' => $this->diagnoses->all(),
            'symptoms' => $this->symptoms->all(),
        ]);
    }

    public function store(StoreRuleRequest $request): RedirectResponse
    {
        [$data, $groups] = $this->prepareData($request->validated());

        $rule = $this->rules->create($data, $groups);

        return redirect()
            ->route('admin.intelligence.rules.show', $rule)
            ->with('success', 'Rule berhasil dibuat sebagai draf. Tinjau lalu publikasikan.');
    }

    public function show(Rule $rule): View
    {
        $rule = $this->rules->find($rule->getKey());

        return view('admin.intelligence.rules.show', [
            'rule' => $rule,
            'version' => $this->rules->latestVersion($rule),
        ]);
    }

    public function edit(Rule $rule): View
    {
        $rule = $this->rules->find($rule->getKey());

        return view('admin.intelligence.rules.form', [
            'rule' => $rule,
            'version' => $this->rules->latestVersion($rule),
            'diagnoses' => $this->diagnoses->all(),
            'symptoms' => $this->symptoms->all(),
        ]);
    }

    public function update(UpdateRuleRequest $request, Rule $rule): RedirectResponse
    {
        [$data, $groups] = $this->prepareData($request->validated());

        $rule = $this->rules->update($rule, $data, $groups);

        return redirect()
            ->route('admin.intelligence.rules.show', $rule)
            ->with('success', 'Rule berhasil diperbarui. Perubahan tersimpan sebagai draf baru.');
    }

    public function publish(Rule $rule): RedirectResponse
    {
        try {
            $this->rules->publish($rule);

            return back()->with('success', 'Rule berhasil dipublikasikan.');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function deactivate(Rule $rule): RedirectResponse
    {
        $this->rules->deactivate($rule);

        return back()->with('success', 'Rule berhasil dinonaktifkan.');
    }

    public function activate(Rule $rule): RedirectResponse
    {
        try {
            $this->rules->activate($rule);

            return back()->with('success', 'Rule berhasil diaktifkan.');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: array<string, mixed>, 1: array<int, array<string, mixed>>}
     */
    private function prepareData(array $data): array
    {
        $groups = $data['groups'] ?? [];
        unset($data['groups'], $data['is_active']);

        return [$data, $groups];
    }
}
