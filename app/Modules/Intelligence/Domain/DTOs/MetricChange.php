<?php

namespace App\Modules\Intelligence\Domain\DTOs;

final readonly class MetricChange
{
    public function __construct(
        public float $difference,
        public ?float $percentage,
    ) {}
}
