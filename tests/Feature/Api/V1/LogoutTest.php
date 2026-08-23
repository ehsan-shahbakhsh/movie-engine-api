<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('logs out the user correctly', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/auth/logout');

    $response
        ->assertOk()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('returns unauthorized without token', function () {
    $response = $this->postJson('/api/v1/auth/logout');

    $response
        ->assertUnauthorized()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ]);
});
