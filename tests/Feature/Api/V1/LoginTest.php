<?php

use App\Models\User;

it('authenticates a user and returns an access token', function () {
    $user = User::factory()->create(['password' => 'secret123']);

    $payload = [
        'email' => $user->email,
        'password' => 'secret123',
    ];

    $response = $this->postJson('/api/v1/auth/login', $payload);

    $response
        ->assertOk()
        ->assertJsonPaths([
            'data.user.id' => $user->id,
            'data.user.email' => $user->email,
        ])
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'is_verified',
                    'created_at',
                ],
                'authorization' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'expires_at',
                ],
            ],
            'meta',
            'errors',
            'error_code',
        ]);
});

it('returns unauthorized error for invalid credentials', function () {
    $user = User::factory()->create(['password' => 'secret123']);

    $payload = [
        'email' => $user->email,
        'password' => 'secret1234',
    ];

    $response = $this->postJson('/api/v1/auth/login', $payload);

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

it('returns validation errors for invalid input data', function (?string $email, string $password, string $errorField) {
    $payload = [
        'email' => $email,
        'password' => $password,
    ];

    $response = $this->postJson('/api/v1/auth/login', $payload);

    $response
        ->assertUnprocessable()
        ->assertExactJsonStructure([
            'success',
            'code',
            'message',
            'data',
            'meta',
            'errors',
            'error_code',
        ])
        ->assertJsonValidationErrorFor($errorField);
})->with([
    'missing email' => [null, 'secret123', 'email'],
    'invalid email format' => ['ehsan.com', 'secret123', 'email'],
    'short password' => ['ehsan@example.com', 'secret1', 'password'],
]);
