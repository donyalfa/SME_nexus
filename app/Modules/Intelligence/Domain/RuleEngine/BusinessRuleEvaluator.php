<?php

namespace App\Modules\Intelligence\Domain\RuleEngine;

use App\Modules\Intelligence\Domain\DTOs\RuleCondition;

final class BusinessRuleEvaluator
{
    /**
     * Conditions in a group are ANDed; groups are ORed.
     *
     * @param  array<RuleCondition>  $conditions
     * @param  array<string, int|float|string|null>  $metrics
     */
    public function matches(array $conditions, array $metrics): bool
    {
        if ($conditions === []) {
            return false;
        }

        $groups = [];

        foreach ($conditions as $condition) {
            $groups[$condition->groupNumber][] = $condition;
        }

        foreach ($groups as $groupConditions) {
            $groupMatches = true;

            foreach ($groupConditions as $condition) {
                $metricValue = $metrics[$condition->metricCode] ?? null;

                if (! is_numeric($metricValue) || ! is_finite((float) $metricValue)) {
                    $groupMatches = false;
                    break;
                }

                if (! $condition->operator->matches((float) $metricValue, $condition->threshold)) {
                    $groupMatches = false;
                    break;
                }
            }

            if ($groupMatches) {
                return true;
            }
        }

        return false;
    }
}
