<?php

namespace App\Modules\Intelligence\Services;

use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Repositories\RecommendationRepository;

class RecommendationService
{
    public function __construct(
        private readonly RecommendationRepository $recommendations,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->recommendations->paginate($filters);
    }

    public function find(int $id): Recommendation
    {
        return $this->recommendations->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Recommendation
    {
        $steps = $this->pullSteps($data);

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $recommendation = $this->recommendations->create($data);
        $recommendation->syncSteps($steps);

        $this->auditLogger->log(
            'admin.recommendation.created',
            'autopsy_recommendation',
            $recommendation->getKey(),
            [],
            array_merge($data, ['action_steps' => $steps])
        );

        return $recommendation;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Recommendation $recommendation, array $data): Recommendation
    {
        $steps = $this->pullSteps($data);

        $oldValues = $recommendation->only([
            'id_diagnosis',
            'code',
            'title',
            'description',
            'priority',
            'is_active',
        ]);
        $oldValues['action_steps'] = $recommendation->steps->pluck('step')->all();

        $data['updated_by'] = auth()->id();
        $recommendation = $this->recommendations->update($recommendation, $data);
        $recommendation->syncSteps($steps);

        $this->auditLogger->log(
            'admin.recommendation.updated',
            'autopsy_recommendation',
            $recommendation->getKey(),
            $oldValues,
            array_merge($data, ['action_steps' => $steps])
        );

        return $recommendation;
    }

    /**
     * Ambil daftar langkah dari payload lalu buang dari data yang disimpan sebagai kolom.
     *
     * @param  array<string, mixed>  $data
     * @return array<int, string>
     */
    private function pullSteps(array &$data): array
    {
        $steps = array_values(array_filter(
            $data['action_steps'] ?? [],
            fn ($step): bool => is_string($step) && trim($step) !== ''
        ));

        unset($data['action_steps']);

        return $steps;
    }

    public function deactivate(Recommendation $recommendation): Recommendation
    {
        $recommendation = $this->recommendations->deactivate($recommendation);

        $this->auditLogger->log('admin.recommendation.deactivated', 'autopsy_recommendation', $recommendation->getKey(), ['is_active' => true], ['is_active' => false]);

        return $recommendation;
    }

    public function activate(Recommendation $recommendation): Recommendation
    {
        $recommendation->update(['is_active' => true, 'updated_by' => auth()->id()]);

        $this->auditLogger->log('admin.recommendation.activated', 'autopsy_recommendation', $recommendation->getKey(), ['is_active' => false], ['is_active' => true]);

        return $recommendation->refresh();
    }
}
