<?php

namespace App\Actions\Auth;

use App\Data\Auth\AuthorizationData;
use App\Data\Auth\AuthResultData;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

final class RegisterAction
{
    public function execute(string $name, string $email, string $password): AuthResultData
    {
        $user = User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        event(new Registered($user));

        $expirationMinutes = config('sanctum.expiration');
        $expirationTime = $expirationMinutes ? now()->addMinutes($expirationMinutes) : null;

        $userToken = $user->createToken('Auth Token', expiresAt: $expirationTime);

        return new AuthResultData(
            user: $user,
            authorization: new AuthorizationData(
                accessToken: $userToken->plainTextToken,
                tokenType: 'Bearer',
                expiresIn: $expirationMinutes
                    ? $expirationMinutes * 60
                    : null,
                expiresAt: $expirationTime,
            ),
        );
    }
}
