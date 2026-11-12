<?php

namespace App\Modules\Intelligence\Services;

use App\Modules\Intelligence\Domain\Enums\RuleVersionStatus;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Models\RuleVersion;
use App\Modules\Intelligence\Repositories\RuleRepository;
use DomainException;
use Illuminate\Support\Arr;

class RuleService
{
    public function __construct(
        private readonly RuleRepository $rules,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->rules->paginate($filters);
    }

    public function find(int $id): Rule
    {
        return $this->rules->find($id);
    }

    public function latestVersion(Rule $rule): ?RuleVersion
    {
        return $this->rules->latestVersion($rule);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $groups
     */
    public function create(array $data, array $groups): Rule
    {
        $versionNotes = $data['version_notes'] ?? null;
        unset($data['version_notes']);

        $data['is_active'] = false;
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $rule = $this->rules->create($data);
        $this->rules->createVersion($rule, [
            'version' => 1,
            'status' => RuleVersionStatus::Draft->value,
            'is_active' => false,
            'notes' => $versionNotes,
            'created_by' => auth()->id(),
        ], $groups);

        $this->auditLogger->log('admin.rule.created', 'autopsy_rule', $rule->getKey(), [], [
            ...Arr::except($data, ['created_by', 'updated_by']),
            'versions' => 1,
            'groups' => count($groups),
        ]);

        return $rule->refresh()->load(['diagnosis', 'activeVersion', 'versions']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $groups
     */
    public function update(Rule $rule, array $data, array $groups): Rule
    {
        $versionNotes = $data['version_notes'] ?? null;
        unset($data['version_notes']);

        $oldValues = $rule->only(['code', 'name', 'description', 'id_diagnosis', 'severity', 'priority', 'is_active']);
        $data['updated_by'] = auth()->id();

        $rule = $this->rules->update($rule, $data);
        $latestVersion = $this->rules->latestVersion($rule);

        if ($latestVersion && $latestVersion->status === RuleVersionStatus::Draft) {
            $this->rules->updateDraftVersion($latestVersion, ['notes' => $versionNotes], $groups);
        } else {
            $this->rules->createVersion($rule, [
                'version' => $latestVersion ? $latestVersion->version + 1 : 1,
                'status' => RuleVersionStatus::Draft->value,
                'is_active' => false,
                'notes' => $versionNotes,
                'created_by' => auth()->id(),
            ], $groups);
        }

        $this->auditLogger->log('admin.rule.updated', 'autopsy_rule', $rule->getKey(), $oldValues, [
            ...Arr::except($data, ['created_by', 'updated_by']),
            'groups' => count($groups),
        ]);

        return $rule->refresh()->load(['diagnosis', 'activeVersion', 'versions']);
    }

    public function publish(Rule $rule): Rule
    {
        $version = $this->rules->latestVersion($rule);

        if (! $version) {
            throw new DomainException('Rule belum memiliki versi.');
        }

        $conditionCount = $version->groups()->withCount('conditions')->get()->sum('conditions_count');

        if ($conditionCount === 0) {
            throw new DomainException('Rule harus memiliki minimal satu kondisi sebelum dipublikasikan.');
        }

        $hasInactiveSymptom = $version->groups
            ->flatMap(fn ($group) => $group->conditions)
            ->contains(fn ($condition) => ! $condition->symptom || ! $condition->symptom->is_active);

        if ($hasInactiveSymptom) {
            throw new DomainException('Rule menggunakan gejala yang sudah dinonaktifkan. Perbarui kondisi sebelum publikasi.');
        }

        if (! $rule->diagnosis?->is_active) {
            throw new DomainException('Diagnosis rule sedang nonaktif. Aktifkan diagnosis sebelum publikasi.');
        }

        $this->rules->publishVersion($version);
        $rule->update(['is_active' => true, 'updated_by' => auth()->id()]);

        $this->auditLogger->log('admin.rule.published', 'autopsy_rule', $rule->getKey(), [], [
            'version' => $version->version,
            'specificity' => $conditionCount,
        ]);

        return $rule->refresh()->load(['diagnosis', 'activeVersion', 'versions']);
    }

    public function deactivate(Rule $rule): Rule
    {
        $rule = $this->rules->deactivate($rule);

        $this->auditLogger->log('admin.rule.deactivated', 'autopsy_rule', $rule->getKey(), ['is_active' => true], ['is_active' => false]);

        return $rule;
    }

    public function activate(Rule $rule): Rule
    {
        $hasPublishedVersion = $rule->versions()
            ->where('status', RuleVersionStatus::Published->value)
            ->exists();

        if (! $hasPublishedVersion) {
            throw new DomainException('Rule belum memiliki versi yang dipublikasikan.');
        }

        $rule = $this->rules->activate($rule);

        $this->auditLogger->log('admin.rule.activated', 'autopsy_rule', $rule->getKey(), ['is_active' => false], ['is_active' => true]);

        return $rule;
    }
}
