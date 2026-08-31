<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

it('returns success when the verification email is sent', function () {
    $user = User::factory()->unverified()->create();
    Sanctum::actingAs($user);

    Notification::fake();

    $response = $this->postJson('/api/v1/auth/email/verification-notification');

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

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('returns bad request when the user has already verified their email', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/auth/email/verification-notification');

    $response
        ->assertBadRequest()
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

it('returns unauthorized when the user is not authenticated', function () {
    $response = $this->postJson('/api/v1/auth/email/verification-notification');

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
