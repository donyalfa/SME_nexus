<?php

namespace App\Modules\Intelligence\Services;

use App\Modules\Intelligence\Models\Symptom;
use App\Modules\Intelligence\Repositories\SymptomRepository;

class SymptomService
{
    public function __construct(
        private readonly SymptomRepository $symptoms,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->symptoms->paginate($filters);
    }

    public function allActive()
    {
        return $this->symptoms->allActive();
    }

    public function all()
    {
        return $this->symptoms->all();
    }

    public function find(int $id): Symptom
    {
        return $this->symptoms->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Symptom
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $symptom = $this->symptoms->create($data);

        $this->auditLogger->log('admin.symptom.created', 'autopsy_symptom', $symptom->getKey(), [], $data);

        return $symptom;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Symptom $symptom, array $data): Symptom
    {
        $oldValues = $symptom->only([
            'code',
            'name',
            'description',
            'metric_key',
            'operator',
            'threshold',
            'severity',
            'is_active',
        ]);

        $data['updated_by'] = auth()->id();
        $symptom = $this->symptoms->update($symptom, $data);

        $this->auditLogger->log('admin.symptom.updated', 'autopsy_symptom', $symptom->getKey(), $oldValues, $data);

        return $symptom;
    }

    public function deactivate(Symptom $symptom): Symptom
    {
        $symptom = $this->symptoms->deactivate($symptom);

        $this->auditLogger->log('admin.symptom.deactivated', 'autopsy_symptom', $symptom->getKey(), ['is_active' => true], ['is_active' => false]);

        return $symptom;
    }

    public function activate(Symptom $symptom): Symptom
    {
        $symptom->update(['is_active' => true, 'updated_by' => auth()->id()]);

        $this->auditLogger->log('admin.symptom.activated', 'autopsy_symptom', $symptom->getKey(), ['is_active' => false], ['is_active' => true]);

        return $symptom->refresh();
    }
}
