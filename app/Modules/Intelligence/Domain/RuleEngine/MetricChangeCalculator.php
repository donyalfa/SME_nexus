<?php

namespace App\Modules\Intelligence\Domain\RuleEngine;

use App\Modules\Intelligence\Domain\DTOs\MetricChange;

final class MetricChangeCalculator
{
    public function calculate(float $currentValue, float $comparisonValue): MetricChange
    {
        $difference = $currentValue - $comparisonValue;
        $percentage = $comparisonValue === 0.0
            ? null
            : ($difference / $comparisonValue) * 100;

        return new MetricChange($difference, $percentage);
    }
}
