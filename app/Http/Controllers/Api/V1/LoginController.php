<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::query()->firstWhere('email', $validated['email']);

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return ApiResponse::unauthorized(__('auth.failed'));
        }

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
        ], 'ورود با موفقیت انجام شد.');
    }
}
