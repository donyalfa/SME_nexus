<?php

use App\Modules\Intelligence\Models\Diagnosis;
use App\Modules\Intelligence\Models\Recommendation;
use App\Modules\Intelligence\Models\Symptom;
use App\Modules\Intelligence\Models\BusinessCategory;
use App\Shared\Models\BusinessData;
use App\Modules\Intelligence\Models\SmeProfile;
use App\Shared\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

test('admin dapat menambah dan memperbarui pengguna', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.users.store'), [
            'full_name' => 'Pemilik Baru',
            'email' => 'pemilik.baru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'sme_owner',
            'is_verified' => '1',
        ])
        ->assertRedirect(route('admin.intelligence.users.index'));

    $user = User::query()->where('email', 'pemilik.baru@example.com')->firstOrFail();

    expect($user->full_name)->toBe('Pemilik Baru')
        ->and($user->role)->toBe('sme_owner')
        ->and($user->is_verified)->toBeTrue()
        ->and(Hash::check('password123', $user->password))->toBeTrue();

    $this->assertDatabaseHas('autopsy_audit_logs', [
        'action' => 'admin.user.created',
        'entity_type' => 'user',
        'entity_id' => $user->id_user,
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.intelligence.users.update', $user), [
            'full_name' => 'Pemilik Diperbarui',
            'email' => 'pemilik.baru@example.com',
            'password' => '',
            'password_confirmation' => '',
            'role' => 'sme_owner',
            'is_verified' => '1',
        ])
        ->assertRedirect(route('admin.intelligence.users.index'));

    $user->refresh();

    expect($user->full_name)->toBe('Pemilik Diperbarui')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

test('admin tidak dapat menghapus akun sendiri', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.intelligence.users.destroy', $this->admin))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', ['id_user' => $this->admin->id_user]);
});

test('admin tidak dapat menurunkan role akun sendiri', function () {
    $response = $this->actingAs($this->admin)
        ->from(route('admin.intelligence.users.edit', $this->admin))
        ->put(route('admin.intelligence.users.update', $this->admin), [
            'full_name' => $this->admin->full_name,
            'email' => $this->admin->email,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'sme_owner',
            'is_verified' => '1',
        ]);

    $response
        ->assertSessionHas('error')
        ->assertRedirect(route('admin.intelligence.users.edit', $this->admin));

    expect($this->admin->refresh()->role)->toBe('admin');
});

test('admin dapat mengelola kategori bisnis dan kategori terpakai tidak dapat dihapus', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.categories.store'), [
            'category_name' => 'Kuliner',
            'deskripsi' => 'Makanan dan minuman',
        ])
        ->assertRedirect(route('admin.intelligence.categories.index'));

    $category = BusinessCategory::query()->where('category_name', 'Kuliner')->firstOrFail();

    $this->actingAs($this->admin)
        ->put(route('admin.intelligence.categories.update', $category), [
            'category_name' => 'Kuliner Nusantara',
            'deskripsi' => 'Makanan dan minuman lokal',
        ])
        ->assertRedirect(route('admin.intelligence.categories.index'));

    expect($category->refresh()->category_name)->toBe('Kuliner Nusantara');

    $owner = User::factory()->create();
    SmeProfile::query()->create([
        'id_user' => $owner->id_user,
        'id_category' => $category->id_category,
        'business_name' => 'Warung Uji',
        'city' => 'Bandung',
        'established_year' => 2020,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.intelligence.categories.destroy', $category))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('business_categories', ['id_category' => $category->id_category]);
});

test('admin dapat mengelola profil umkm dan profil dengan data bisnis tidak dapat dihapus', function () {
    $owner = User::factory()->create();
    $category = BusinessCategory::query()->create([
        'category_name' => 'Retail',
        'deskripsi' => 'Perdagangan eceran',
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.profiles.store'), [
            'id_user' => $owner->id_user,
            'id_category' => $category->id_category,
            'business_name' => 'Toko Uji',
            'legal_type' => 'Perorangan',
            'address' => 'Jl. Uji No. 1',
            'city' => 'Jakarta',
            'established_year' => 2019,
        ])
        ->assertRedirect(route('admin.intelligence.profiles.index'));

    $profile = SmeProfile::query()->where('business_name', 'Toko Uji')->firstOrFail();

    $this->actingAs($this->admin)
        ->put(route('admin.intelligence.profiles.update', $profile), [
            'id_user' => $owner->id_user,
            'id_category' => $category->id_category,
            'business_name' => 'Toko Uji Baru',
            'legal_type' => 'Perorangan',
            'address' => 'Jl. Uji No. 2',
            'city' => 'Jakarta',
            'established_year' => 2019,
        ])
        ->assertRedirect(route('admin.intelligence.profiles.index'));

    expect($profile->refresh()->business_name)->toBe('Toko Uji Baru');

    BusinessData::query()->create([
        'id_profile' => $profile->id_profile,
        'period_date' => '2026-10-01',
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.intelligence.profiles.destroy', $profile))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('sme_profiles', ['id_profile' => $profile->id_profile]);
});

test('admin dapat mengelola gejala dan menonaktifkannya', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.symptoms.store'), [
            'code' => 'revenue_declined_test',
            'name' => 'Penurunan omzet uji',
            'description' => 'Uji gejala',
            'metric_key' => 'revenue_change_percent',
            'operator' => 'less_than',
            'threshold' => -10,
            'severity' => 'high',
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.intelligence.symptoms.index'));

    $symptom = Symptom::query()->where('code', 'revenue_declined_test')->firstOrFail();

    expect($symptom->operator->value)->toBe('less_than')
        ->and($symptom->severity->value)->toBe('high');

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.symptoms.deactivate', $symptom))
        ->assertRedirect();

    expect($symptom->refresh()->is_active)->toBeFalse();

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.symptoms.activate', $symptom))
        ->assertRedirect();

    expect($symptom->refresh()->is_active)->toBeTrue();
});

test('admin dapat mengelola diagnosis dan rekomendasi', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.diagnoses.store'), [
            'code' => 'low_sales_test',
            'name' => 'Penjualan rendah uji',
            'description' => 'Diagnosis uji',
            'severity' => 'critical',
            'priority' => 10,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.intelligence.diagnoses.index'));

    $diagnosis = Diagnosis::query()->where('code', 'low_sales_test')->firstOrFail();

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.recommendations.store'), [
            'id_diagnosis' => $diagnosis->id,
            'code' => 'improve_marketing_test',
            'title' => 'Tingkatkan pemasaran',
            'description' => 'Rekomendasi uji',
            'action_steps_text' => "Audit kanal pemasaran\nNegosiasi biaya iklan\n",
            'priority' => 20,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.intelligence.recommendations.index'));

    $recommendation = Recommendation::query()->where('code', 'improve_marketing_test')->firstOrFail();

    expect($recommendation->action_steps)->toBe(['Audit kanal pemasaran', 'Negosiasi biaya iklan']);

    $this->actingAs($this->admin)
        ->post(route('admin.intelligence.recommendations.deactivate', $recommendation))
        ->assertRedirect();

    expect($recommendation->refresh()->is_active)->toBeFalse();
});
