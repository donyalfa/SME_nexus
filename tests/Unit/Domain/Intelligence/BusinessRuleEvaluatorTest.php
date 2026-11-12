<?php

use App\Modules\Intelligence\Domain\DTOs\RuleCondition;
use App\Modules\Intelligence\Domain\Enums\RuleOperator;
use App\Modules\Intelligence\Domain\RuleEngine\BusinessRuleEvaluator;

function evaluateRule(array $conditions, array $metrics): bool
{
    return (new BusinessRuleEvaluator)->matches($conditions, $metrics);
}

test('requires all conditions in the same group to match', function () {
    $conditions = [
        new RuleCondition('revenue_change_percent', RuleOperator::LessThan, -10.0, 1),
        new RuleCondition('customer_change_percent', RuleOperator::LessThan, -20.0, 1),
    ];

    expect(evaluateRule($conditions, [
        'revenue_change_percent' => -30.0,
        'customer_change_percent' => -15.0,
    ]))->toBeFalse();
});

test('matches when any condition group fully matches', function () {
    $conditions = [
        new RuleCondition('revenue_change_percent', RuleOperator::LessThan, -10.0, 1),
        new RuleCondition('customer_change_percent', RuleOperator::LessThan, -20.0, 1),
        new RuleCondition('orders_change_percent', RuleOperator::LessThan, -20.0, 2),
    ];

    expect(evaluateRule($conditions, [
        'revenue_change_percent' => -30.0,
        'customer_change_percent' => -5.0,
        'orders_change_percent' => -25.0,
    ]))->toBeTrue();
});

test('does not match when a required metric is missing', function () {
    $conditions = [
        new RuleCondition('revenue_change_percent', RuleOperator::LessThan, -10.0),
    ];

    expect(evaluateRule($conditions, []))->toBeFalse();
});

test('does not match an empty rule', function () {
    expect(evaluateRule([], ['revenue_change_percent' => -30.0]))->toBeFalse();
});

test('supports greater than comparisons', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::GreaterThan, 10.0),
    ], ['metric' => 11.0]))->toBeTrue();
});

test('supports greater than or equal comparisons', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::GreaterThanOrEqual, 10.0),
    ], ['metric' => 10.0]))->toBeTrue();
});

test('supports less than comparisons', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::LessThan, 10.0),
    ], ['metric' => 9.0]))->toBeTrue();
});

test('supports less than or equal comparisons', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::LessThanOrEqual, 10.0),
    ], ['metric' => 10.0]))->toBeTrue();
});

test('supports equality comparisons', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::Equal, 10.0),
    ], ['metric' => 10.0]))->toBeTrue();
});

test('supports inequality comparisons', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::NotEqual, 10.0),
    ], ['metric' => 9.0]))->toBeTrue();
});

test('does not match when a metric value is not numeric', function () {
    expect(evaluateRule([
        new RuleCondition('metric', RuleOperator::GreaterThan, 10.0),
    ], ['metric' => 'not-a-number']))->toBeFalse();
});
