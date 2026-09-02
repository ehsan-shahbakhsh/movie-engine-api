<?php

namespace App\Actions\Auth;

use App\Data\Auth\AuthorizationData;
use App\Data\Auth\AuthResultData;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class LoginAction
{
    /**
     * @throws InvalidCredentialsException
     */
    public function execute(string $email, string $password): AuthResultData
    {
        $user = User::query()->firstWhere('email', $email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException;
        }

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
