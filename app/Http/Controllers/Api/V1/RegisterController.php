<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::query()->create($validated);

        event(new Registered($user));

        $expirationMinutes = config('sanctum.expiration');
        $expirationTime = $expirationMinutes ? now()->addMinutes($expirationMinutes) : null;

        $userToken = $user->createToken('Auth Token', expiresAt: $expirationTime);

        return ApiResponse::success([
            'user' => UserResource::make($user),
            'authorization' => [
                'access_token' => $userToken->plainTextToken,
                'token_type' => 'Bearer',
                'expires_in' => $expirationMinutes
                    ? $expirationMinutes * 60
                    : null,
                'expires_at' => $expirationTime,
            ],
        ], 'ثبت‌نام با موفقیت انجام شد. لطفاً ایمیل خود را تأیید کنید.');
    }
}
