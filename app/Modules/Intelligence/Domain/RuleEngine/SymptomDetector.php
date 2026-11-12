<?php

namespace App\Modules\Intelligence\Domain\RuleEngine;

final class SymptomDetector
{
    /**
     * @param array<string, float|null> $metrics
     * @return string[]
     */
    public function detect(array $metrics): array
    {
        $symptoms = [];

        if (isset($metrics['revenue_change_percent']) && $metrics['revenue_change_percent'] !== null && $metrics['revenue_change_percent'] < -10.0) {
            $symptoms[] = 'revenue_declined';
        }

        if (isset($metrics['customer_change_percent']) && $metrics['customer_change_percent'] !== null && $metrics['customer_change_percent'] < -20.0) {
            $symptoms[] = 'customers_declined';
        }

        if (isset($metrics['operational_cost_ratio']) && $metrics['operational_cost_ratio'] !== null && $metrics['operational_cost_ratio'] > 70.0) {
            $symptoms[] = 'operational_cost_high';
        }

        return $symptoms;
    }
}
