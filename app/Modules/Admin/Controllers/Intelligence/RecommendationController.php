<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\StoreRecommendationRequest;
use App\Modules\Admin\Requests\Intelligence\UpdateRecommendationRequest;
use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Services\DiagnosisService;
use App\Modules\Intelligence\Services\RecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly RecommendationService $recommendations,
        private readonly DiagnosisService $diagnoses,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'id_diagnosis', 'is_active']);

        if (($filters['is_active'] ?? '') === '') {
            $filters['is_active'] = null;
        }

        return view('admin.intelligence.recommendations.index', [
            'recommendations' => $this->recommendations->paginate($filters),
            'filters' => $filters,
            'diagnoses' => $this->diagnoses->allActive(),
        ]);
    }

    public function create(): View
    {
        return view('admin.intelligence.recommendations.form', [
            'recommendation' => new Recommendation(['priority' => 100, 'is_active' => true]),
            'diagnoses' => $this->diagnoses->all(),
        ]);
    }

    public function store(StoreRecommendationRequest $request): RedirectResponse
    {
        $this->recommendations->create($this->prepareData($request->validated()));

        return redirect()
            ->route('admin.intelligence.recommendations.index')
            ->with('success', 'Rekomendasi berhasil ditambahkan.');
    }

    public function edit(Recommendation $recommendation): View
    {
        return view('admin.intelligence.recommendations.form', [
            'recommendation' => $recommendation,
            'diagnoses' => $this->diagnoses->all(),
        ]);
    }

    public function update(UpdateRecommendationRequest $request, Recommendation $recommendation): RedirectResponse
    {
        $this->recommendations->update($recommendation, $this->prepareData($request->validated()));

        return redirect()
            ->route('admin.intelligence.recommendations.index')
            ->with('success', 'Rekomendasi berhasil diperbarui.');
    }

    public function deactivate(Recommendation $recommendation): RedirectResponse
    {
        $this->recommendations->deactivate($recommendation);

        return back()->with('success', 'Rekomendasi berhasil dinonaktifkan.');
    }

    public function activate(Recommendation $recommendation): RedirectResponse
    {
        $this->recommendations->activate($recommendation);

        return back()->with('success', 'Rekomendasi berhasil diaktifkan.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareData(array $data): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) ($data['action_steps_text'] ?? '')) ?: [];
        $data['action_steps'] = array_values(array_filter(array_map('trim', $lines)));
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        unset($data['action_steps_text']);

        return $data;
    }
}
