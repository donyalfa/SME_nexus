<?php

use App\Modules\Intelligence\Domain\DTOs\MetricChange;
use App\Modules\Intelligence\Domain\RuleEngine\SymptomDetector;

test('detects revenue decline when change is below threshold', function () {
    $symptoms = (new SymptomDetector)->detect([
        'revenue_change_percent' => -15.0,
    ]);

    expect($symptoms)->toContain('revenue_declined');
});

test('does not detect revenue decline when change is above threshold', function () {
    $symptoms = (new SymptomDetector)->detect([
        'revenue_change_percent' => -5.0,
    ]);

    expect($symptoms)->not->toContain('revenue_declined');
});

test('detects customer decline when change is below threshold', function () {
    $symptoms = (new SymptomDetector)->detect([
        'customer_change_percent' => -25.0,
    ]);

    expect($symptoms)->toContain('customers_declined');
});

test('detects operational cost pressure when ratio exceeds threshold', function () {
    $symptoms = (new SymptomDetector)->detect([
        'operational_cost_ratio' => 75.0,
    ]);

    expect($symptoms)->toContain('operational_cost_high');
});

test('returns empty array when no symptoms are detected', function () {
    $symptoms = (new SymptomDetector)->detect([
        'revenue_change_percent' => 5.0,
        'customer_change_percent' => 3.0,
        'operational_cost_ratio' => 40.0,
    ]);

    expect($symptoms)->toBe([]);
});
