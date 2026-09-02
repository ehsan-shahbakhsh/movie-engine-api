<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\LoginAction;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Responses\ApiResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws InvalidCredentialsException
     */
    #[OA\Post(
        path: "/api/v1/auth/login",
        description: "Authenticates a user with their email and password and returns the authenticated user and access token.",
        summary: "Authenticate a user",
        requestBody: new OA\RequestBody(
            description: "User credentials used to authenticate the user",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LoginRequest"),
        ),
        tags: ["Authentication"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_OK),
                        new OA\Property(property: "message", type: "string", example: "ورود با موفقیت انجام شد."),
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
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
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
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_UNAUTHORIZED),
                        new OA\Property(property: "message", type: "string", example: "اطلاعات وارد شده غلط میباشد.", nullable: true),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ]
                )
            ),
        ],
    )]
    public function __invoke(LoginRequest $request, LoginAction $action)
    {
        $validated = $request->validated();

        $result = $action->execute($validated['email'], $validated['password']);

        return ApiResponse::success([
            'user' => $result->user->toResource(),
            'authorization' => [
                'access_token' => $result->authorization->accessToken,
                'token_type' => $result->authorization->tokenType,
                'expires_in' => $result->authorization->expiresIn,
                'expires_at' => $result->authorization->expiresAt,
            ],
        ], 'ورود با موفقیت انجام شد.');
    }
}
