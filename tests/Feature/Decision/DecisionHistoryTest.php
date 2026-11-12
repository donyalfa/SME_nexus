<?php

use App\Modules\Intelligence\Models\Analysis;
use App\Shared\Models\User;

test('sme owner dapat membuka halaman decision', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('decision.index'))
        ->assertOk()
        ->assertSee('Riwayat Analisis Bisnis');
});

test('halaman decision menampilkan riwayat analisis milik sendiri', function () {
    $owner = User::factory()->create();

    Analysis::create([
        'id_user' => $owner->id_user,
        'period_prev_date' => '2026-10-01',
        'period_current_date' => '2026-11-01',
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

    $response = $this->actingAs($owner)
        ->get(route('decision.index'))
        ->assertOk()
        ->assertSee('Perlu Perhatian')
        ->assertSee('01 Oct 2026')
        ->assertSee('Lihat hasil');

    $rows = $response->viewData('analyses')->getCollection();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->id_user)->toEqual($owner->id_user);
});

test('halaman decision tidak menampilkan analisis pemilik lain', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    Analysis::create([
        'id_user' => $owner->id_user,
        'omzet_prev' => 50000,
        'omzet_current' => 50000,
        'customer_prev' => 500,
        'customer_current' => 500,
        'sales_prev' => 1000,
        'sales_current' => 1000,
        'product_count' => 15,
        'avg_price' => 50000,
        'marketing_spend' => 1000,
        'operational_cost' => 1000,
        'omzet_change' => 0,
        'customer_change' => 0,
        'sales_change' => 0,
        'status' => 'ok',
    ]);

    Analysis::create([
        'id_user' => $other->id_user,
        'period_prev_date' => '1999-01-01',
        'period_current_date' => '1999-02-01',
        'omzet_prev' => 77777,
        'omzet_current' => 77777,
        'customer_prev' => 777,
        'customer_current' => 777,
        'sales_prev' => 7777,
        'sales_current' => 7777,
        'product_count' => 7,
        'avg_price' => 77777,
        'marketing_spend' => 777,
        'operational_cost' => 777,
        'omzet_change' => 0,
        'customer_change' => 0,
        'sales_change' => 0,
        'status' => 'ok',
    ]);

    $response = $this->actingAs($owner)
        ->get(route('decision.index'))
        ->assertOk();

    $rows = $response->viewData('analyses')->getCollection();

    expect($rows)->toHaveCount(1)
        ->and($rows->first()->id_user)->toEqual($owner->id_user)
        ->and($rows->where('id_user', $other->id_user))->toBeEmpty();
});

test('admin tidak dapat membuka halaman decision', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('decision.index'))
        ->assertForbidden();
});
