<?php

use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\Rule;
use App\Modules\Intelligence\Models\Symptom;
use App\Shared\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->diagnosis = Diagnosis::query()->create([
        'code' => 'revenue_problem',
        'name' => 'Masalah pendapatan',
        'description' => 'Diagnosis uji',
        'severity' => 'high',
        'priority' => 10,
        'is_active' => true,
    ]);
    $this->symptomA = Symptom::query()->create([
        'code' => 'revenue_down',
        'name' => 'Omzet turun',
        'metric_key' => 'revenue_change_percent',
        'operator' => 'less_than',
        'threshold' => -10,
        'severity' => 'high',
        'is_active' => true,
    ]);
    $this->symptomB = Symptom::query()->create([
        'code' => 'customers_down',
        'name' => 'Pelanggan turun',
        'metric_key' => 'customer_change_percent',
        'operator' => 'less_than',
        'threshold' => -20,
        'severity' => 'medium',
        'is_active' => true,
    ]);
    $this->symptomC = Symptom::query()->create([
        'code' => 'cost_high',
        'name' => 'Biaya tinggi',
        'metric_key' => 'operating_expense_ratio',
        'operator' => 'greater_than',
        'threshold' => 70,
        'severity' => 'high',
        'is_active' => true,
    ]);
});

test('admin dapat membuat rule dengan grup kondisi and dan or', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.store'), [
            'code' => 'revenue_decline_rule',
            'name' => 'Penurunan pendapatan',
            'description' => 'Rule uji',
            'id_diagnosis' => $this->diagnosis->id,
            'severity' => 'high',
            'priority' => 10,
            'is_active' => '1',
            'version_notes' => 'Versi awal',
            'groups' => [
                [
                    'label' => 'Penjualan',
                    'conditions' => [
                        ['id_symptom' => $this->symptomA->id],
                        ['id_symptom' => $this->symptomB->id, 'is_negated' => '1'],
                    ],
                ],
                [
                    'label' => 'Biaya',
                    'conditions' => [
                        ['id_symptom' => $this->symptomC->id],
                    ],
                ],
            ],
        ])
        ->assertRedirect();

    $rule = Rule::query()->where('code', 'revenue_decline_rule')->firstOrFail();
    $version = $rule->versions()->firstOrFail();

    expect($version->version)->toBe(1)
        ->and($version->status->value)->toBe('draft')
        ->and($version->is_active)->toBeFalse()
        ->and($rule->is_active)->toBeFalse()
        ->and($version->groups)->toHaveCount(2)
        ->and($version->groups->first()->conditions)->toHaveCount(2)
        ->and($version->groups->first()->conditions->firstWhere('id_symptom', $this->symptomB->id)->is_negated)->toBeTrue();

    $this->assertDatabaseHas('autopsy_audit_logs', [
        'action' => 'admin.rule.created',
        'entity_type' => 'autopsy_rule',
        'entity_id' => $rule->id,
    ]);
});

test('admin dapat mempublikasikan rule dan perubahan setelah publikasi membuat versi baru', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.store'), [
            'code' => 'revenue_decline_rule',
            'name' => 'Penurunan pendapatan',
            'id_diagnosis' => $this->diagnosis->id,
            'severity' => 'high',
            'priority' => 10,
            'is_active' => '1',
            'groups' => [
                ['label' => 'Grup 1', 'conditions' => [['id_symptom' => $this->symptomA->id]]],
            ],
        ]);

    $rule = Rule::query()->where('code', 'revenue_decline_rule')->firstOrFail();

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.publish', $rule))
        ->assertRedirect();

    $versionOne = $rule->versions()->where('version', 1)->firstOrFail();

    expect($versionOne->status->value)->toBe('published')
        ->and($versionOne->is_active)->toBeTrue()
        ->and($rule->refresh()->is_active)->toBeTrue();

    $this->actingAs($this->admin)
        ->put(route('admin.intelligence.rules.update', $rule), [
            'code' => 'revenue_decline_rule',
            'name' => 'Penurunan pendapatan diperbarui',
            'id_diagnosis' => $this->diagnosis->id,
            'severity' => 'critical',
            'priority' => 5,
            'is_active' => '1',
            'version_notes' => 'Perubahan kondisi',
            'groups' => [
                ['label' => 'Grup baru', 'conditions' => [['id_symptom' => $this->symptomC->id]]],
            ],
        ])
        ->assertRedirect();

    $versionOne->refresh();
    $versionTwo = $rule->versions()->where('version', 2)->firstOrFail();

    expect($versionOne->status->value)->toBe('published')
        ->and($versionOne->is_active)->toBeTrue()
        ->and($versionTwo->status->value)->toBe('draft')
        ->and($versionTwo->is_active)->toBeFalse()
        ->and($rule->refresh()->is_active)->toBeTrue();

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.publish', $rule))
        ->assertRedirect();

    expect($versionOne->refresh()->is_active)->toBeFalse()
        ->and($versionTwo->refresh()->status->value)->toBe('published')
        ->and($versionTwo->is_active)->toBeTrue();
});

test('halaman index menampilkan aksi sesuai status publikasi rule', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.store'), [
            'code' => 'index_status_rule',
            'name' => 'Rule status index',
            'id_diagnosis' => $this->diagnosis->id,
            'severity' => 'high',
            'priority' => 10,
            'groups' => [
                ['label' => 'Grup 1', 'conditions' => [['id_symptom' => $this->symptomA->id]]],
            ],
        ])
        ->assertRedirect();

    $rule = Rule::query()->where('code', 'index_status_rule')->firstOrFail();

    $this->actingAs($this->admin)
        ->get(route('admin.intelligence.rules.index'))
        ->assertOk()
        ->assertSee('Publikasikan');

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.publish', $rule))
        ->assertRedirect();

    $this->actingAs($this->admin)
        ->get(route('admin.intelligence.rules.index'))
        ->assertOk()
        ->assertSee('Nonaktifkan');

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.deactivate', $rule))
        ->assertRedirect();

    $this->actingAs($this->admin)
        ->get(route('admin.intelligence.rules.index'))
        ->assertOk()
        ->assertSee('Aktifkan');
});

test('rule menolak gejala duplikat dalam satu grup', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.store'), [
            'code' => 'duplicate_symptom_rule',
            'name' => 'Duplikat',
            'id_diagnosis' => $this->diagnosis->id,
            'severity' => 'high',
            'priority' => 10,
            'groups' => [
                [
                    'label' => 'Grup 1',
                    'conditions' => [
                        ['id_symptom' => $this->symptomA->id],
                        ['id_symptom' => $this->symptomA->id],
                    ],
                ],
            ],
        ])
        ->assertSessionHasErrors('groups.0.conditions');

    $this->assertDatabaseMissing('autopsy_rules', ['code' => 'duplicate_symptom_rule']);
});

test('halaman detail dan edit rule dapat dibuka', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.rules.store'), [
            'code' => 'detail_rule',
            'name' => 'Rule Detail',
            'id_diagnosis' => $this->diagnosis->id,
            'severity' => 'medium',
            'priority' => 50,
            'groups' => [
                ['label' => 'Grup 1', 'conditions' => [['id_symptom' => $this->symptomA->id]]],
            ],
        ]);

    $rule = Rule::query()->where('code', 'detail_rule')->firstOrFail();

    $this->actingAs($this->admin)
        ->get(route('admin.intelligence.rules.show', $rule))
        ->assertOk()
        ->assertSee('Rule Detail')
        ->assertSee('Riwayat Versi');

    $this->actingAs($this->admin)
        ->get(route('admin.intelligence.rules.edit', $rule))
        ->assertOk()
        ->assertSee('Pratinjau Rule');
});
