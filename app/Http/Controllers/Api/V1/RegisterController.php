<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    #[OA\Post(
        path: "/api/v1/auth/register",
        description: "Creates a new user account and returns the authenticated user and access token.",
        summary: "Register a new user",
        requestBody: new OA\RequestBody(
            description: "User object that needs to be created",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/RegisterRequest"),
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_CREATED),
                        new OA\Property(property: "message", type: "string", example: "ثبت‌نام با موفقیت انجام شد. لطفاً ایمیل خود را تأیید کنید."),
                        new OA\Property(property: "data", properties: [
                            new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
                            new OA\Property(property: "authorization", properties: [
                                new OA\Property(property: "access_token", type: "string", example: "1|zC2m1jaauofXISNcFrsywtKkogrRnT6FByPhz64v6ca3db20"),
                                new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                                new OA\Property(property: "expires_in", type: "integer", example: 2592000, nullable: true),
                                new OA\Property(property: "expires_at", type: "string", format: "date-time", example: "2026-01-01T00:00:00Z", nullable: true),
                            ], type: "object"),
                        ], type: "object"),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_UNPROCESSABLE_ENTITY),
                        new OA\Property(property: "message", type: "string", example: "اطلاعات ورودی معتبر نیست.", nullable: true),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ]
                )
            ),
        ],
    )]
    public function __invoke(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::query()->create($validated);

        event(new Registered($user));

        $expirationMinutes = config('sanctum.expiration');
        $expirationTime = $expirationMinutes ? now()->addMinutes($expirationMinutes) : null;

        $userToken = $user->createToken('Auth Token', expiresAt: $expirationTime);

        return ApiResponse::created([
            'user' => $user->toResource(),
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
