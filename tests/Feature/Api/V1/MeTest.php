<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns the user details correctly', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/auth/me');

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.id' => $user->id,
            'data.email' => $user->email,
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'is_verified',
                'created_at',
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns unauthorized without token', function () {
    $response = $this->getJson('/api/v1/auth/me');

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
