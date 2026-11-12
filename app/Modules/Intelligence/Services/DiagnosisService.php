<?php

namespace App\Modules\Intelligence\Services;

use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Repositories\DiagnosisRepository;

class DiagnosisService
{
    public function __construct(
        private readonly DiagnosisRepository $diagnoses,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->diagnoses->paginate($filters);
    }

    public function allActive()
    {
        return $this->diagnoses->allActive();
    }

    public function find(int $id): Diagnosis
    {
        return $this->diagnoses->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Diagnosis
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $diagnosis = $this->diagnoses->create($data);

        $this->auditLogger->log('admin.diagnosis.created', 'autopsy_diagnosis', $diagnosis->getKey(), [], $data);

        return $diagnosis;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Diagnosis $diagnosis, array $data): Diagnosis
    {
        $oldValues = $diagnosis->only(['code', 'name', 'description', 'severity', 'priority', 'is_active']);

        $data['updated_by'] = auth()->id();
        $diagnosis = $this->diagnoses->update($diagnosis, $data);

        $this->auditLogger->log('admin.diagnosis.updated', 'autopsy_diagnosis', $diagnosis->getKey(), $oldValues, $data);

        return $diagnosis;
    }

    public function deactivate(Diagnosis $diagnosis): Diagnosis
    {
        $diagnosis = $this->diagnoses->deactivate($diagnosis);

        $this->auditLogger->log('admin.diagnosis.deactivated', 'autopsy_diagnosis', $diagnosis->getKey(), ['is_active' => true], ['is_active' => false]);

        return $diagnosis;
    }

    public function activate(Diagnosis $diagnosis): Diagnosis
    {
        $diagnosis->update(['is_active' => true, 'updated_by' => auth()->id()]);

        $this->auditLogger->log('admin.diagnosis.activated', 'autopsy_diagnosis', $diagnosis->getKey(), ['is_active' => false], ['is_active' => true]);

        return $diagnosis->refresh();
    }
}
