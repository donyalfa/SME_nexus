<?php

namespace App\Modules\Intelligence\Services;

use App\Modules\Intelligence\Domain\RuleEngine\DiagnosisRuleEngine;
use App\Modules\Intelligence\Models\RuleVersion;
use App\Modules\Intelligence\Repositories\RecommendationRepository;
use App\Modules\Intelligence\Repositories\RuleRepository;
use App\Modules\Intelligence\Services\DTOs\DiagnosisEvaluationResult;

class DiagnosisEvaluationService
{
    public function __construct(
        private readonly SymptomDetectionService $symptomDetector,
        private readonly RuleRepository $rules,
        private readonly RecommendationRepository $recommendations,
        private readonly DiagnosisRuleEngine $ruleEngine,
    ) {}

    /**
     * Evaluasi metrik bisnis menjadi gejala, rule yang cocok, dan diagnosis utama.
     *
     * @param  array<string, float|int|string|null>  $metrics
     */
    public function evaluate(array $metrics): DiagnosisEvaluationResult
    {
        $detectedSymptoms = $this->symptomDetector->detect($metrics);
        $detectedSymptomIds = $detectedSymptoms->pluck('id')->all();
        $candidates = [];

        foreach ($this->rules->activePublishedForEvaluation() as $version) {
            $rule = $version->rule;
            $diagnosis = $version->diagnosis ?? $rule?->diagnosis;

            if (! $rule || ! $diagnosis || ! $diagnosis->is_active) {
                continue;
            }

            if ($this->hasInactiveSymptom($version)) {
                continue;
            }

            $groups = $version->groups
                ->map(fn ($group) => $group->conditions
                    ->map(fn ($condition) => [
                        'id_symptom' => $condition->id_symptom,
                        'is_negated' => $condition->is_negated,
                    ])
                    ->values()
                    ->all())
                ->values()
                ->all();

            $specificity = $this->ruleEngine->matchedGroupSpecificity($groups, $detectedSymptomIds);

            if ($specificity === null) {
                continue;
            }

            $candidates[] = [
                'rule' => $rule,
                'version' => $version,
                'diagnosis' => $diagnosis,
                'severity' => $version->severity ?? $rule->severity,
                'priority' => $version->priority ?? $rule->priority,
                'specificity' => $specificity,
                'code' => $rule->code,
                'rule_id' => $rule->getKey(),
            ];
        }

        $rankedCandidates = $this->ruleEngine->rank($candidates);
        $primaryCandidate = $rankedCandidates[0] ?? null;
        $recommendations = $primaryCandidate
            ? $this->recommendations->activeForDiagnosis($primaryCandidate['diagnosis']->getKey())
            : collect();

        return new DiagnosisEvaluationResult(
            metrics: $metrics,
            detectedSymptoms: $detectedSymptoms,
            matchedCandidates: $rankedCandidates,
            primaryCandidate: $primaryCandidate,
            recommendations: $recommendations,
        );
    }

    private function hasInactiveSymptom(RuleVersion $version): bool
    {
        return $version->groups->contains(
            fn ($group) => $group->conditions->contains(
                fn ($condition) => ! $condition->symptom || ! $condition->symptom->is_active
            )
        );
    }
}
