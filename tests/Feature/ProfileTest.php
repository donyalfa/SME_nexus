<?php

use App\Modules\Intelligence\Models\BusinessCategory;
use App\Modules\Intelligence\Models\SmeProfile;
use App\Shared\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->full_name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertFalse($user->is_verified);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'full_name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertTrue($user->refresh()->is_verified);
});

test('email verification status is reset when the email address changes', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->patch('/profile', [
            'full_name' => 'Test User',
            'email' => 'new-email@example.com',
        ]);

    $this->assertFalse($user->refresh()->is_verified);
});

test('profile rejects a name longer than the database column', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->patch('/profile', [
            'full_name' => str_repeat('A', 101),
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasErrors('full_name')
        ->assertRedirect('/profile');

    expect($user->refresh()->full_name)->not->toBe(str_repeat('A', 101));
});

test('user with an SME profile cannot delete their account', function () {
    $user = User::factory()->create();

    $category = BusinessCategory::query()->create([
        'category_name' => 'Retail',
        'deskripsi' => 'Perdagangan eceran',
    ]);

    SmeProfile::query()->create([
        'id_user' => $user->id_user,
        'id_category' => $category->id_category,
        'business_name' => 'Toko Uji',
        'city' => 'Jakarta',
        'established_year' => 2020,
    ]);

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertAuthenticated();
    $this->assertNotNull($user->fresh());
    $this->assertDatabaseHas('sme_profiles', [
        'id_user' => $user->id_user,
        'business_name' => 'Toko Uji',
    ]);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
