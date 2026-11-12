<?php

use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Modules\Intelligence\Domain\RuleEngine\DiagnosisRuleEngine;

function candidate(string $code, Severity $severity, int $priority, int $specificity): array
{
    return [
        'code' => $code,
        'severity' => $severity,
        'priority' => $priority,
        'specificity' => $specificity,
    ];
}

test('grup kondisi membutuhkan semua gejala dengan and', function () {
    $engine = new DiagnosisRuleEngine;

    $groups = [
        [
            ['id_symptom' => 1, 'is_negated' => false],
            ['id_symptom' => 2, 'is_negated' => false],
        ],
    ];

    expect($engine->matches($groups, [1]))->toBeFalse()
        ->and($engine->matches($groups, [1, 2]))->toBeTrue();
});

test('rule cocok bila salah satu grup terpenuhi dengan or', function () {
    $engine = new DiagnosisRuleEngine;

    $groups = [
        [
            ['id_symptom' => 1, 'is_negated' => false],
            ['id_symptom' => 2, 'is_negated' => false],
        ],
        [
            ['id_symptom' => 3, 'is_negated' => false],
        ],
    ];

    expect($engine->matches($groups, [2, 3]))->toBeTrue()
        ->and($engine->matches($groups, [2]))->toBeFalse();
});

test('kondisi negasi cocok saat gejala tidak terdeteksi', function () {
    $engine = new DiagnosisRuleEngine;

    $groups = [
        [
            ['id_symptom' => 1, 'is_negated' => false],
            ['id_symptom' => 2, 'is_negated' => true],
        ],
    ];

    expect($engine->matches($groups, [1]))->toBeTrue()
        ->and($engine->matches($groups, [1, 2]))->toBeFalse();
});

test('rule tanpa grup tidak pernah cocok', function () {
    expect((new DiagnosisRuleEngine)->matches([], [1, 2, 3]))->toBeFalse();
});

test('spesifisitas diambil dari grup yang cocok dengan kondisi terbanyak', function () {
    $engine = new DiagnosisRuleEngine;

    $groups = [
        [
            ['id_symptom' => 1, 'is_negated' => false],
        ],
        [
            ['id_symptom' => 2, 'is_negated' => false],
            ['id_symptom' => 3, 'is_negated' => false],
            ['id_symptom' => 4, 'is_negated' => false],
        ],
    ];

    expect($engine->matchedGroupSpecificity($groups, [1, 2, 3, 4]))->toBe(3)
        ->and($engine->matchedGroupSpecificity($groups, [1]))->toBe(1)
        ->and($engine->matchedGroupSpecificity($groups, [9]))->toBeNull();
});

test('konflik diselesaikan berdasarkan severity tertinggi', function () {
    $ranked = (new DiagnosisRuleEngine)->rank([
        candidate('low_rule', Severity::Low, 1, 1),
        candidate('critical_rule', Severity::Critical, 100, 1),
        candidate('medium_rule', Severity::Medium, 1, 4),
    ]);

    expect($ranked[0]['code'])->toBe('critical_rule');
});

test('prioritas lebih kecil menang bila severity sama', function () {
    $ranked = (new DiagnosisRuleEngine)->rank([
        candidate('priority_100', Severity::High, 100, 4),
        candidate('priority_10', Severity::High, 10, 1),
    ]);

    expect($ranked[0]['code'])->toBe('priority_10');
});

test('spesifisitas lebih tinggi menang bila severity dan prioritas sama', function () {
    $ranked = (new DiagnosisRuleEngine)->rank([
        candidate('specific_1', Severity::Medium, 50, 1),
        candidate('specific_3', Severity::Medium, 50, 3),
    ]);

    expect($ranked[0]['code'])->toBe('specific_3');
});

test('kode rule menjadi penentu terakhir bila semua nilai sama', function () {
    $ranked = (new DiagnosisRuleEngine)->rank([
        candidate('rule_b', Severity::Medium, 50, 2),
        candidate('rule_a', Severity::Medium, 50, 2),
    ]);

    expect($ranked[0]['code'])->toBe('rule_a');
});
