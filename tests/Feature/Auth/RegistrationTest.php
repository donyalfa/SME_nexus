<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'full_name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice'));

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'full_name' => 'Test User',
        'role' => 'sme_owner',
        'is_verified' => 0,
    ]);
});

test('registration rejects a name longer than the database column', function () {
    $response = $this->from('/register')->post('/register', [
        'full_name' => str_repeat('A', 101),
        'email' => 'long-name@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $response->assertSessionHasErrors('full_name');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'long-name@example.com']);
});
