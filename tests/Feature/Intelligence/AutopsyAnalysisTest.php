<?php

use App\Modules\Intelligence\Models\Analysis;
use App\Shared\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('analisis menampilkan error validasi pada halaman form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/intelligence/autopsy')
        ->post('/intelligence/autopsy/analyze', [
            'omzet_prev' => 0,
            'omzet_current' => 100,
            'customer_prev' => 10,
            'customer_current' => 8,
            'sales_prev' => 10,
            'sales_current' => 8,
            'product_count' => 5,
            'avg_price' => 1000,
            'marketing_spend' => 100,
            'operational_cost' => 100,
        ]);

    $response
        ->assertSessionHasErrors('omzet_prev')
        ->assertRedirect('/intelligence/autopsy');

    $this->actingAs($user)
        ->get('/intelligence/autopsy')
        ->assertOk()
        ->assertSee('Data belum dapat diproses.');
});

test('analisis tersimpan ke database', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/intelligence/autopsy/analyze', [
            'period_prev_date' => '2026-10-01',
            'period_current_date' => '2026-11-01',
            'omzet_prev' => 50000000,
            'omzet_current' => 40000000,
            'customer_prev' => 500,
            'customer_current' => 400,
            'sales_prev' => 1000,
            'sales_current' => 800,
            'product_count' => 15,
            'avg_price' => 50000,
            'marketing_spend' => 5000000,
            'operational_cost' => 30000000,
        ]);

    $analysis = Analysis::query()->sole();

    $response->assertRedirect(route('intelligence.autopsy.show', $analysis));

    $this->assertDatabaseHas('intelligence_analyses', [
        'id_user' => $user->id_user,
        'period_prev_date' => '2026-10-01',
        'period_current_date' => '2026-11-01',
        'omzet_prev' => 50000000,
        'omzet_current' => 40000000,
        'status' => 'critical',
    ]);

    // Omzet/customer/penjualan semuanya turun 20% -> eskalasi jadi kritikal.
    expect((float) $analysis->omzet_change)->toBe(-20.0)
        ->and($analysis->diagnoses)->toBeArray()
        ->and(collect($analysis->diagnoses)->pluck('title')->all())
        ->toContain('Semua Indikator Turun Bersama')
        ->and($analysis->findings()->where('type', 'diagnosis')->count())->toBeGreaterThan(0)
        ->and($analysis->findings()->where('type', 'recommendation')->count())->toBeGreaterThan(0)
        ->and(Schema::hasColumn('intelligence_analyses', 'diagnoses'))->toBeFalse()
        ->and(Schema::hasColumn('intelligence_analyses', 'recommendations'))->toBeFalse();
});

test('hasil rule engine disimpan sebagai baris findings', function () {
    $user = User::factory()->create();
    $now = now();

    $symptomId = DB::table('autopsy_symptoms')->insertGetId([
        'code' => 'test_revenue_drop_rule',
        'name' => 'Test omzet turun',
        'metric_key' => 'revenue_change_percent',
        'operator' => 'less_than',
        'threshold' => -10,
        'severity' => 'high',
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $diagnosisId = DB::table('autopsy_diagnoses')->insertGetId([
        'code' => 'test_diag_omzet',
        'name' => 'Test Omzet Turun',
        'description' => 'Diagnosis uji dari rule engine.',
        'severity' => 'critical',
        'priority' => 1,
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $ruleId = DB::table('autopsy_rules')->insertGetId([
        'code' => 'test_rule_omzet',
        'name' => 'Test rule omzet turun',
        'id_diagnosis' => $diagnosisId,
        'severity' => 'critical',
        'priority' => 1,
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $versionId = DB::table('autopsy_rule_versions')->insertGetId([
        'id_rule' => $ruleId,
        'version' => 1,
        'status' => 'published',
        'is_active' => true,
        'published_at' => $now,
        'id_diagnosis' => $diagnosisId,
        'severity' => 'critical',
        'priority' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $groupId = DB::table('autopsy_rule_groups')->insertGetId([
        'id_rule_version' => $versionId,
        'group_order' => 1,
        'label' => 'Omzet turun',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('autopsy_rule_conditions')->insert([
        'id_rule_group' => $groupId,
        'id_symptom' => $symptomId,
        'is_negated' => false,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $recommendationId = DB::table('autopsy_recommendations')->insertGetId([
        'id_diagnosis' => $diagnosisId,
        'code' => 'test_rec_omzet',
        'title' => 'Rekomendasi uji',
        'description' => 'Langkah perbaikan dari master data.',
        'action_steps' => json_encode(['Langkah satu', 'Langkah dua']),
        'priority' => 1,
        'is_active' => true,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $this->actingAs($user)->post('/intelligence/autopsy/analyze', [
        'omzet_prev' => 100,
        'omzet_current' => 80,
        'customer_prev' => 100,
        'customer_current' => 100,
        'sales_prev' => 100,
        'sales_current' => 100,
        'product_count' => 10,
        'avg_price' => 10000,
        'marketing_spend' => 0,
        'operational_cost' => 0,
    ])->assertRedirect();

    $analysis = Analysis::query()->sole();

    expect((int) $analysis->primary_diagnosis_id)->toBe($diagnosisId)
        ->and((int) $analysis->primary_rule_version_id)->toBe($versionId)
        ->and($analysis->status)->toBe('critical');

    $symptom = $analysis->symptoms()->where('code', 'test_revenue_drop_rule')->sole();

    expect((int) $symptom->id_symptom)->toBe($symptomId)
        ->and($symptom->metric_key)->toBe('revenue_change_percent')
        ->and(Schema::hasColumn('intelligence_analyses', 'detected_symptoms'))->toBeFalse();

    $diagnosis = $analysis->findings()->where('type', 'diagnosis')->sole();

    expect($diagnosis->source)->toBe('rule')
        ->and((int) $diagnosis->ref_id)->toBe($diagnosisId)
        ->and((int) $diagnosis->id_rule_version)->toBe($versionId)
        ->and($diagnosis->code)->toBe('test_diag_omzet')
        ->and($diagnosis->title)->toBe('Test Omzet Turun');

    $recommendation = $analysis->findings()->where('type', 'recommendation')->sole();

    expect((int) $recommendation->ref_id)->toBe($recommendationId)
        ->and($recommendation->priority_label)->toBe('high')
        ->and($recommendation->action_steps)->toBe(['Langkah satu', 'Langkah dua']);
});

test('analisis wajib dimiliki oleh pengguna untuk dibuka', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $analysis = Analysis::create([
        'id_user' => $owner->id_user,
        'omzet_prev' => 50000,
        'omzet_current' => 40000,
        'customer_prev' => 500,
        'customer_current' => 400,
        'sales_prev' => 1000,
        'sales_current' => 800,
        'product_count' => 15,
        'avg_price' => 50000,
        'marketing_spend' => 5000000,
        'operational_cost' => 30000000,
        'omzet_change' => -20,
        'customer_change' => -20,
        'sales_change' => -20,
        'status' => 'critical',
    ]);

    $route = '/intelligence/autopsy/' . $analysis->getKey();

    $this->actingAs($owner)
        ->get($route)
        ->assertOk()
        ->assertSee('Hasil Diagnosis');

    $this->actingAs($other)
        ->get($route)
        ->assertNotFound();
});
