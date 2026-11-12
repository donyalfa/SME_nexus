<?php

use App\Modules\Intelligence\Domain\Enums\RuleVersionStatus;
use App\Modules\Intelligence\Domain\Enums\Severity;
use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Models\Symptom;
use App\Shared\Models\User;
use App\Modules\Intelligence\Repositories\RuleRepository;
use App\Modules\Intelligence\Services\DiagnosisEvaluationService;
use App\Modules\Intelligence\Services\RuleService;
use Illuminate\Support\Str;

function diagnosisEvaluationSymptom(
    string $code,
    string $metricKey,
    string $operator = 'less_than',
    float $threshold = -10,
    bool $isActive = true,
): Symptom {
    return Symptom::query()->create([
        'code' => $code,
        'name' => 'Gejala '.$code,
        'metric_key' => $metricKey,
        'operator' => $operator,
        'threshold' => $threshold,
        'severity' => 'medium',
        'is_active' => $isActive,
    ]);
}

function diagnosisEvaluationDiagnosis(string $code, string $severity = 'high', int $priority = 10): Diagnosis
{
    return Diagnosis::query()->create([
        'code' => $code,
        'name' => 'Diagnosis '.$code,
        'severity' => $severity,
        'priority' => $priority,
        'is_active' => true,
    ]);
}

function diagnosisEvaluationGroups(int ...$symptomIds): array
{
    return [[
        'label' => 'Grup uji',
        'conditions' => array_map(
            fn (int $symptomId) => ['id_symptom' => $symptomId],
            $symptomIds
        ),
    ]];
}

function diagnosisEvaluationRule(
    Diagnosis $diagnosis,
    array $groups,
    array $overrides = [],
    bool $publish = true,
): Rule {
    $rule = Rule::query()->create(array_merge([
        'code' => 'rule_'.Str::lower(Str::random(8)),
        'name' => 'Rule '.$diagnosis->code,
        'id_diagnosis' => $diagnosis->id,
        'severity' => 'medium',
        'priority' => 100,
        'is_active' => true,
    ], $overrides));

    $repository = app(RuleRepository::class);
    $version = $repository->createVersion($rule, [
        'version' => 1,
        'status' => RuleVersionStatus::Draft->value,
        'is_active' => false,
        'notes' => 'Uji evaluasi',
    ], $groups);

    if ($publish) {
        $repository->publishVersion($version);
    }

    return $rule->refresh();
}

test('evaluasi menerapkan and dalam grup dan or antar grup', function () {
    $symptomA = diagnosisEvaluationSymptom('eval_symptom_a', 'metric_a');
    $symptomB = diagnosisEvaluationSymptom('eval_symptom_b', 'metric_b');
    $symptomC = diagnosisEvaluationSymptom('eval_symptom_c', 'metric_c');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_and_or');

    diagnosisEvaluationRule($diagnosis, [
        [
            'label' => 'A dan B',
            'conditions' => [
                ['id_symptom' => $symptomA->id],
                ['id_symptom' => $symptomB->id],
            ],
        ],
        [
            'label' => 'C',
            'conditions' => [
                ['id_symptom' => $symptomC->id],
            ],
        ],
    ]);

    $service = app(DiagnosisEvaluationService::class);

    $andResult = $service->evaluate(['metric_a' => -20, 'metric_b' => -20]);
    $orResult = $service->evaluate(['metric_a' => -20, 'metric_b' => 0, 'metric_c' => -20]);
    $noResult = $service->evaluate(['metric_a' => -20, 'metric_b' => 0, 'metric_c' => 0]);

    expect($andResult->hasMatch())->toBeTrue()
        ->and($andResult->primaryDiagnosis()?->code)->toBe('eval_diag_and_or')
        ->and($orResult->hasMatch())->toBeTrue()
        ->and($noResult->hasMatch())->toBeFalse();
});

test('evaluasi mendukung kondisi negasi', function () {
    $symptomA = diagnosisEvaluationSymptom('eval_neg_a', 'metric_a');
    $symptomB = diagnosisEvaluationSymptom('eval_neg_b', 'metric_b');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_negation');

    diagnosisEvaluationRule($diagnosis, [[
        'label' => 'A tanpa B',
        'conditions' => [
            ['id_symptom' => $symptomA->id],
            ['id_symptom' => $symptomB->id, 'is_negated' => true],
        ],
    ]]);

    $service = app(DiagnosisEvaluationService::class);

    $negatedResult = $service->evaluate(['metric_a' => -20]);
    $presentResult = $service->evaluate(['metric_a' => -20, 'metric_b' => -20]);

    expect($negatedResult->hasMatch())->toBeTrue()
        ->and($negatedResult->primaryDiagnosis()?->code)->toBe('eval_diag_negation')
        ->and($presentResult->hasMatch())->toBeFalse();
});

test('konflik diagnosis diselesaikan berdasarkan severity terlebih dahulu', function () {
    $symptom = diagnosisEvaluationSymptom('eval_severity_symptom', 'metric_severity');
    $criticalDiagnosis = diagnosisEvaluationDiagnosis('eval_diag_critical', 'critical', 999);
    $highDiagnosis = diagnosisEvaluationDiagnosis('eval_diag_high', 'high', 1);

    diagnosisEvaluationRule($criticalDiagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'critical_rule',
        'severity' => 'critical',
        'priority' => 999,
    ]);
    diagnosisEvaluationRule($highDiagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'high_rule',
        'severity' => 'high',
        'priority' => 1,
    ]);

    $result = app(DiagnosisEvaluationService::class)->evaluate(['metric_severity' => -20]);

    expect($result->primaryDiagnosis()?->code)->toBe('eval_diag_critical')
        ->and($result->matchedCandidates)->toHaveCount(2);
});

test('prioritas lebih kecil menang bila severity sama', function () {
    $symptom = diagnosisEvaluationSymptom('eval_priority_symptom', 'metric_priority');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_priority', 'high', 10);

    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'priority_100_rule',
        'severity' => 'high',
        'priority' => 100,
    ]);
    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'priority_10_rule',
        'severity' => 'high',
        'priority' => 10,
    ]);

    $result = app(DiagnosisEvaluationService::class)->evaluate(['metric_priority' => -20]);

    expect($result->primaryRule()?->code)->toBe('priority_10_rule');
});

test('spesifisitas lebih tinggi menang bila severity dan prioritas sama', function () {
    $symptomA = diagnosisEvaluationSymptom('eval_specific_a', 'metric_specific_a');
    $symptomB = diagnosisEvaluationSymptom('eval_specific_b', 'metric_specific_b');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_specific', 'high', 10);

    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptomA->id), [
        'code' => 'specific_1_rule',
        'severity' => 'high',
        'priority' => 10,
    ]);
    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptomA->id, $symptomB->id), [
        'code' => 'specific_2_rule',
        'severity' => 'high',
        'priority' => 10,
    ]);

    $result = app(DiagnosisEvaluationService::class)->evaluate([
        'metric_specific_a' => -20,
        'metric_specific_b' => -20,
    ]);

    expect($result->primaryRule()?->code)->toBe('specific_2_rule')
        ->and($result->primaryCandidate['specificity'])->toBe(2);
});

test('kode rule menjadi penentu terakhir bila semua nilai sama', function () {
    $symptom = diagnosisEvaluationSymptom('eval_code_symptom', 'metric_code');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_code', 'high', 10);

    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'z_rule',
        'severity' => 'high',
        'priority' => 10,
    ]);
    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'a_rule',
        'severity' => 'high',
        'priority' => 10,
    ]);

    $result = app(DiagnosisEvaluationService::class)->evaluate(['metric_code' => -20]);

    expect($result->primaryRule()?->code)->toBe('a_rule');
});

test('rule nonaktif dan versi draf tidak dievaluasi', function () {
    $symptom = diagnosisEvaluationSymptom('eval_inactive_symptom', 'metric_inactive');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_inactive');

    $inactiveRule = diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'inactive_rule',
        'is_active' => false,
    ]);
    $draftRule = diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'draft_rule',
    ], publish: false);

    $result = app(DiagnosisEvaluationService::class)->evaluate(['metric_inactive' => -20]);

    expect($inactiveRule->is_active)->toBeFalse()
        ->and($draftRule->is_active)->toBeTrue()
        ->and($result->hasMatch())->toBeFalse();
});

test('diagnosis nonaktif tidak menghasilkan diagnosis', function () {
    $symptom = diagnosisEvaluationSymptom('eval_inactive_diag_symptom', 'metric_inactive_diag');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_inactive_target');

    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'inactive_diag_rule',
    ]);

    $diagnosis->update(['is_active' => false]);

    expect(app(DiagnosisEvaluationService::class)->evaluate(['metric_inactive_diag' => -20])->hasMatch())->toBeFalse();
});

test('rule dengan gejala nonaktif tidak dievaluasi meskipun kondisinya negasi', function () {
    $symptom = diagnosisEvaluationSymptom('eval_negated_inactive', 'metric_negated_inactive');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_negated_inactive');

    diagnosisEvaluationRule($diagnosis, [[
        'label' => 'Tanpa gejala',
        'conditions' => [
            ['id_symptom' => $symptom->id, 'is_negated' => true],
        ],
    ]], ['code' => 'negated_inactive_rule']);

    $service = app(DiagnosisEvaluationService::class);

    expect($service->evaluate([])->hasMatch())->toBeTrue();

    $symptom->update(['is_active' => false]);

    expect($service->evaluate([])->hasMatch())->toBeFalse();
});

test('rekomendasi aktif diurutkan berdasarkan prioritas', function () {
    $symptom = diagnosisEvaluationSymptom('eval_recommendation_symptom', 'metric_recommendation');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_recommendation');

    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'recommendation_rule',
    ]);

    Recommendation::query()->create([
        'id_diagnosis' => $diagnosis->id,
        'code' => 'recommendation_20',
        'title' => 'Rekomendasi 20',
        'priority' => 20,
        'is_active' => true,
    ]);
    Recommendation::query()->create([
        'id_diagnosis' => $diagnosis->id,
        'code' => 'recommendation_10',
        'title' => 'Rekomendasi 10',
        'priority' => 10,
        'is_active' => true,
    ]);
    Recommendation::query()->create([
        'id_diagnosis' => $diagnosis->id,
        'code' => 'recommendation_inactive',
        'title' => 'Rekomendasi Nonaktif',
        'priority' => 1,
        'is_active' => false,
    ]);

    $result = app(DiagnosisEvaluationService::class)->evaluate(['metric_recommendation' => -20]);

    expect($result->recommendations->pluck('title')->all())
        ->toBe(['Rekomendasi 10', 'Rekomendasi 20']);
});

test('rule dapat diaktifkan kembali setelah dinonaktifkan', function () {
    $symptom = diagnosisEvaluationSymptom('eval_reactivate_symptom', 'metric_reactivate');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_reactivate');
    $rule = diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'reactivate_rule',
    ]);

    $service = app(DiagnosisEvaluationService::class);
    $ruleService = app(RuleService::class);

    expect($service->evaluate(['metric_reactivate' => -20])->hasMatch())->toBeTrue();

    $ruleService->deactivate($rule);

    expect($service->evaluate(['metric_reactivate' => -20])->hasMatch())->toBeFalse();

    $ruleService->activate($rule);

    expect($service->evaluate(['metric_reactivate' => -20])->hasMatch())->toBeTrue();
});

test('rule tanpa versi terpublikasi tidak dapat diaktifkan', function () {
    $symptom = diagnosisEvaluationSymptom('eval_no_publish_symptom', 'metric_no_publish');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_no_publish_diag');
    $rule = diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'no_publish_rule',
        'is_active' => false,
    ], publish: false);

    expect(fn () => app(RuleService::class)->activate($rule))
        ->toThrow(DomainException::class);
});

test('publikasi menolak rule yang memakai gejala nonaktif', function () {
    $symptom = diagnosisEvaluationSymptom('eval_inactive_publish_symptom', 'metric_inactive_publish', isActive: false);
    $diagnosis = diagnosisEvaluationDiagnosis('eval_inactive_publish_diag');
    $rule = diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'inactive_publish_rule',
    ], publish: false);

    expect(fn () => app(RuleService::class)->publish($rule))
        ->toThrow(DomainException::class);
});

test('publikasi menolak rule dengan diagnosis nonaktif', function () {
    $symptom = diagnosisEvaluationSymptom('eval_inactive_publish_diag_symptom', 'metric_inactive_publish_diag');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_inactive_publish_target');
    $rule = diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'inactive_publish_diag_rule',
    ], publish: false);

    $diagnosis->update(['is_active' => false]);

    expect(fn () => app(RuleService::class)->publish($rule))
        ->toThrow(DomainException::class);
});

test('admin dapat menjalankan rule tester tanpa menyimpan analisis', function () {
    $admin = User::factory()->admin()->create();
    $symptom = diagnosisEvaluationSymptom('eval_tester_symptom', 'metric_tester');
    $diagnosis = diagnosisEvaluationDiagnosis('eval_diag_tester');

    diagnosisEvaluationRule($diagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'tester_rule',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.intelligence.rules.tester'))
        ->assertOk()
        ->assertSee('metric_tester');

    $this->actingAs($admin)
        ->post(route('admin.intelligence.rules.tester.run'), [
            'metrics' => ['metric_tester' => 'bukan-angka'],
        ])
        ->assertSessionHasErrors('metrics.metric_tester');

    $this->actingAs($admin)
        ->post(route('admin.intelligence.rules.tester.run'), [
            'metrics' => ['metric_tester' => -25],
        ])
        ->assertOk()
        ->assertSee('Hasil Uji')
        ->assertSee('Diagnosis eval_diag_tester');

    $this->assertDatabaseCount('autopsy_audit_logs', 0);
});

test('perubahan metadata rule tidak memengaruhi versi yang sedang terpublikasi', function () {
    $symptom = diagnosisEvaluationSymptom('eval_snapshot_symptom', 'metric_snapshot');
    $oldDiagnosis = diagnosisEvaluationDiagnosis('eval_snapshot_old', 'high', 100);
    $newDiagnosis = diagnosisEvaluationDiagnosis('eval_snapshot_new', 'critical', 1);

    $rule = diagnosisEvaluationRule($oldDiagnosis, diagnosisEvaluationGroups($symptom->id), [
        'code' => 'snapshot_rule',
        'severity' => 'high',
        'priority' => 100,
    ]);

    $ruleService = app(RuleService::class);
    $rule = $ruleService->update($rule, [
        'code' => 'snapshot_rule',
        'name' => $rule->name,
        'description' => $rule->description,
        'id_diagnosis' => $newDiagnosis->id,
        'severity' => 'critical',
        'priority' => 1,
        'is_active' => true,
        'version_notes' => 'Draft perubahan metadata',
    ], diagnosisEvaluationGroups($symptom->id));

    $evaluation = app(DiagnosisEvaluationService::class);
    $beforePublish = $evaluation->evaluate(['metric_snapshot' => -20]);

    expect($beforePublish->primaryDiagnosis()?->code)->toBe('eval_snapshot_old')
        ->and($beforePublish->primaryCandidate['severity'])->toBe(Severity::High)
        ->and($beforePublish->primaryCandidate['priority'])->toBe(100);

    $ruleService->publish($rule);

    $afterPublish = $evaluation->evaluate(['metric_snapshot' => -20]);

    expect($afterPublish->primaryDiagnosis()?->code)->toBe('eval_snapshot_new')
        ->and($afterPublish->primaryCandidate['severity'])->toBe(Severity::Critical)
        ->and($afterPublish->primaryCandidate['priority'])->toBe(1);
});
