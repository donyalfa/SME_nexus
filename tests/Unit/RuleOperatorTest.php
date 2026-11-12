<?php

use App\Modules\Intelligence\Domain\Enums\RuleOperator;

test('operator sama dengan memakai toleransi float', function () {
    expect(RuleOperator::Equal->matches(0.1 + 0.2, 0.3))->toBeTrue()
        ->and(RuleOperator::NotEqual->matches(0.1 + 0.2, 0.3))->toBeFalse();
});

test('operator tidak sama dengan memakai toleransi float', function () {
    expect(RuleOperator::NotEqual->matches(0.1 + 0.2, 0.4))->toBeTrue()
        ->and(RuleOperator::Equal->matches(0.1 + 0.2, 0.4))->toBeFalse();
});
