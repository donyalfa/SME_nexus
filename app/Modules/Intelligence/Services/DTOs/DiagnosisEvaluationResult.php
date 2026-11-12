<?php

namespace App\Modules\Intelligence\Services\DTOs;

use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Models\RuleVersion;
use App\Modules\Intelligence\Models\Symptom;
use Illuminate\Support\Collection;

final readonly class DiagnosisEvaluationResult
{
    /**
     * @param  array<string, float|int|string|null>  $metrics
     * @param  Collection<int, Symptom>  $detectedSymptoms
     * @param  array<int, array<string, mixed>>  $matchedCandidates
     * @param  array<string, mixed>|null  $primaryCandidate
     * @param  Collection<int, Recommendation>  $recommendations
     */
    public function __construct(
        public array $metrics,
        public Collection $detectedSymptoms,
        public array $matchedCandidates,
        public ?array $primaryCandidate,
        public Collection $recommendations,
    ) {}

    public function primaryRule(): ?Rule
    {
        return $this->primaryCandidate['rule'] ?? null;
    }

    public function primaryVersion(): ?RuleVersion
    {
        return $this->primaryCandidate['version'] ?? null;
    }

    public function primaryDiagnosis(): ?Diagnosis
    {
        return $this->primaryCandidate['diagnosis'] ?? null;
    }

    public function hasMatch(): bool
    {
        return $this->primaryCandidate !== null;
    }
}
