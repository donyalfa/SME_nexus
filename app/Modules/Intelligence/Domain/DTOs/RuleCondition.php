<?php

namespace App\Modules\Intelligence\Domain\DTOs;

use App\Modules\Intelligence\Domain\Enums\RuleOperator;

final readonly class RuleCondition
{
    public function __construct(
        public string $metricCode,
        public RuleOperator $operator,
        public float $threshold,
        public int $groupNumber = 1,
    ) {}
}
