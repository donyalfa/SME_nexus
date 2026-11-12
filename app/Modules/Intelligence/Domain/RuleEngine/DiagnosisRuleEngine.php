<?php

namespace App\Modules\Intelligence\Domain\RuleEngine;

use App\Modules\Intelligence\Domain\Enums\Severity;

final class DiagnosisRuleEngine
{
    /**
     * Conditions inside a group are ANDed; groups are ORed.
     *
     * @param  array<int, array<int, array{id_symptom: int|string, is_negated?: bool}>>  $groups
     * @param  array<int, int|string>  $detectedSymptomIds
     */
    public function matches(array $groups, array $detectedSymptomIds): bool
    {
        return $this->matchedGroupSpecificity($groups, $detectedSymptomIds) !== null;
    }

    /**
     * Kembalikan jumlah kondisi pada grup yang cocok dengan spesifisitas tertinggi.
     * Null berarti tidak ada grup yang cocok.
     *
     * @param  array<int, array<int, array{id_symptom: int|string, is_negated?: bool}>>  $groups
     * @param  array<int, int|string>  $detectedSymptomIds
     */
    public function matchedGroupSpecificity(array $groups, array $detectedSymptomIds): ?int
    {
        if ($groups === []) {
            return null;
        }

        $detected = [];

        foreach ($detectedSymptomIds as $symptomId) {
            if (is_numeric($symptomId)) {
                $detected[(int) $symptomId] = true;
            }
        }

        $matchedSpecificity = null;

        foreach ($groups as $conditions) {
            if (! is_array($conditions) || $conditions === []) {
                continue;
            }

            $groupMatches = true;

            foreach ($conditions as $condition) {
                if (! is_array($condition) || ! isset($condition['id_symptom'])) {
                    $groupMatches = false;
                    break;
                }

                $isPresent = isset($detected[(int) $condition['id_symptom']]);
                $isNegated = (bool) ($condition['is_negated'] ?? false);

                if ($isPresent === $isNegated) {
                    $groupMatches = false;
                    break;
                }
            }

            if ($groupMatches) {
                $matchedSpecificity = max($matchedSpecificity ?? 0, count($conditions));
            }
        }

        return $matchedSpecificity;
    }

    /**
     * Urutkan kandidat diagnosis: severity, priority, specificity, lalu code.
     *
     * @param  array<int, array{severity: Severity|string, priority: int, specificity: int, code: string, rule_id?: int}>  $candidates
     * @return array<int, array{severity: Severity|string, priority: int, specificity: int, code: string, rule_id?: int}>
     */
    public function rank(array $candidates): array
    {
        usort($candidates, function (array $left, array $right): int {
            $leftSeverity = $left['severity'] instanceof Severity
                ? $left['severity']
                : Severity::from($left['severity']);
            $rightSeverity = $right['severity'] instanceof Severity
                ? $right['severity']
                : Severity::from($right['severity']);

            return $rightSeverity->rank() <=> $leftSeverity->rank()
                ?: ((int) $left['priority'] <=> (int) $right['priority'])
                ?: ((int) $right['specificity'] <=> (int) $left['specificity'])
                ?: strcmp($left['code'], $right['code'])
                ?: ((int) ($left['rule_id'] ?? 0) <=> (int) ($right['rule_id'] ?? 0));
        });

        return $candidates;
    }
}
