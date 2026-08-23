<?php

use App\Models\User;

it('registers a new user successfully', function () {
    $payload = [
        'name' => 'Ehsan',
        'email' => 'ehsan@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

    $response
        ->assertCreated()
        ->assertJsonPaths([
            'data.user.email' => 'ehsan@example.com',
            'data.user.is_verified' => false,
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

    $this->assertDatabaseHas('users', ['email' => 'ehsan@example.com']);
});

it('returns validation errors for invalid input data', function (?string $name, string $email, string $password, string $passwordConfirmation, string $errorField) {
    $payload = [
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'password_confirmation' => $passwordConfirmation,
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

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
    'missing name' => [null, 'ehsan@example.com', 'secret123', 'secret123', 'name'],
    'invalid email format' => ['Ehsan', 'ehsan.com', 'secret123', 'secret123', 'email'],
    'short password' => ['Ehsan', 'ehsan@example.com', 'secret1', 'secret1', 'password'],
    'unmatched password' => ['Ehsan', 'ehsan@example.com', 'secret123', 'secret1', 'password'],
]);

it('fails to register if the email is already taken', function () {
    User::factory()->create(['email' => 'ehsan@example.com']);

    $payload = [
        'name' => 'Ehsan New',
        'email' => 'ehsan@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $response = $this->postJson('/api/v1/auth/register', $payload);

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
        ->assertJsonValidationErrorFor('email');
});
