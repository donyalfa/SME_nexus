<?php

use App\Shared\Models\User;

test('admin dapat membuka seluruh halaman master data', function (string $routeName) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route($routeName))
        ->assertOk();
})->with([
    'admin.intelligence.dashboard',
    'admin.intelligence.users.index',
    'admin.intelligence.users.create',
    'admin.intelligence.profiles.index',
    'admin.intelligence.profiles.create',
    'admin.intelligence.categories.index',
    'admin.intelligence.categories.create',
    'admin.intelligence.symptoms.index',
    'admin.intelligence.symptoms.create',
    'admin.intelligence.diagnoses.index',
    'admin.intelligence.diagnoses.create',
    'admin.intelligence.recommendations.index',
    'admin.intelligence.recommendations.create',
    'admin.intelligence.rules.index',
    'admin.intelligence.rules.create',
    'admin.intelligence.rules.tester',
    'admin.intelligence.audit.index',
]);

test('pemilik umkm tidak dapat membuka halaman admin master data', function (string $routeName) {
    $owner = User::factory()->create();

    $this->actingAs($owner)
        ->get(route($routeName))
        ->assertForbidden();
})->with([
    'admin.intelligence.users.index',
    'admin.intelligence.profiles.index',
    'admin.intelligence.categories.index',
    'admin.intelligence.symptoms.index',
    'admin.intelligence.diagnoses.index',
    'admin.intelligence.recommendations.index',
    'admin.intelligence.rules.index',
    'admin.intelligence.rules.tester',
    'admin.intelligence.audit.index',
]);
