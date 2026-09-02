<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\SendEmailVerificationNotificationAction;
use App\Exceptions\Auth\EmailAlreadyVerifiedException;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class VerificationNotificationController extends Controller
{
    /**
     * Handle the incoming request.
     * @throws EmailAlreadyVerifiedException
     */
    #[OA\Post(
        path: "/api/v1/auth/email/verification-notification",
        description: "Sends a new email verification link to the authenticated user if their email is not already verified.",
        summary: "Resend verification email",
        security: [["sanctum" => []]],
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
                        new OA\Property(property: "message", type: "string", example: "لینک تایید ایمیل با موفقیت ارسال شد."),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_UNAUTHORIZED),
                        new OA\Property(property: "message", type: "string", example: "لطفاً ابتدا وارد حساب کاربری شوید.", nullable: true),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ]
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: "Bad request",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_BAD_REQUEST),
                        new OA\Property(property: "message", type: "string", example: "ایمیل شما قبلاً تایید شده است."),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ]
                )
            ),
        ]
    )]
    public function __invoke(Request $request, SendEmailVerificationNotificationAction $action)
    {
        $action->execute($request->user());

        return ApiResponse::success(message: 'لینک تایید ایمیل با موفقیت ارسال شد.');
    }
}
