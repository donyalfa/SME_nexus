<?php

use App\Modules\Intelligence\Domain\RuleEngine\MetricChangeCalculator;

test('calculates a percentage decrease against the comparison value', function () {
    $change = (new MetricChangeCalculator)->calculate(70.0, 100.0);

    expect($change->difference)->toBe(-30.0)
        ->and($change->percentage)->toBe(-30.0);
});

test('calculates a percentage increase against the comparison value', function () {
    $change = (new MetricChangeCalculator)->calculate(125.0, 100.0);

    expect($change->difference)->toBe(25.0)
        ->and($change->percentage)->toBe(25.0);
});

test('returns zero change when both values are equal', function () {
    $change = (new MetricChangeCalculator)->calculate(42.0, 42.0);

    expect($change->difference)->toBe(0.0)
        ->and($change->percentage)->toBe(0.0);
});

test('returns an undefined percentage when the comparison value is zero', function () {
    $change = (new MetricChangeCalculator)->calculate(12.0, 0.0);

    expect($change->difference)->toBe(12.0)
        ->and($change->percentage)->toBeNull();
});
