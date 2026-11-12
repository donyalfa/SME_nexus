<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\RuleTesterRequest;
use App\Modules\Intelligence\Services\DiagnosisEvaluationService;
use App\Modules\Intelligence\Services\SymptomService;
use Illuminate\View\View;

class RuleTesterController extends Controller
{
    public function __construct(
        private readonly SymptomService $symptoms,
        private readonly DiagnosisEvaluationService $evaluation,
    ) {}

    public function index(): View
    {
        return view('admin.intelligence.rules.tester', [
            'metricInputs' => $this->metricInputs(),
            'metrics' => [],
            'result' => null,
        ]);
    }

    public function run(RuleTesterRequest $request): View
    {
        $metrics = collect($request->validated()['metrics'] ?? [])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->map(fn ($value) => (float) $value)
            ->all();

        return view('admin.intelligence.rules.tester', [
            'metricInputs' => $this->metricInputs(),
            'metrics' => $metrics,
            'result' => $this->evaluation->evaluate($metrics),
        ]);
    }

    /**
     * @return array<int, array{key: string, label: string, hints: array<int, string>}>
     */
    private function metricInputs(): array
    {
        return $this->symptoms->allActive()
            ->groupBy('metric_key')
            ->map(fn ($symptoms, $metricKey) => [
                'key' => (string) $metricKey,
                'label' => $symptoms->pluck('name')->implode(', '),
                'hints' => $symptoms->map(fn ($symptom) => $symptom->conditionLabel())->values()->all(),
            ])
            ->values()
            ->all();
    }
}
