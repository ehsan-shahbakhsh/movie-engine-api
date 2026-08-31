<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\DislikeReactionRequest;
use App\Http\Requests\Api\V1\LikeReactionRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Series;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ReactionController extends Controller
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
        'comment' => Comment::class,
    ];

    #[OA\Post(
        path: "/api/v1/reactions/like",
        description: "Records a 'like' reaction from the authenticated user for a specific movie, series, or comment.",
        summary: "Submit a like reaction",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            description: "The type and ID of the resource to like",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LikeReactionRequest"),
        ),
        tags: ["Reactions"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_OK),
                        new OA\Property(property: "message", type: "string", example: "لایک شما با موفقیت ثبت شد."),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: "Not found",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_NOT_FOUND),
                        new OA\Property(property: "message", type: "string", example: "موردی با این مشخصات یافت نشد.", nullable: true),
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
        ],
    )]
    public function like(LikeReactionRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $model = $modelClass::findOrFail($id);

        $reaction = $model->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($reaction) {
            if ($reaction->type === ReactionType::Like) {
                $reaction->delete();
                $message = 'لایک شما حذف شد.';
                $userReaction = null;
            } else {
                $reaction->update(['type' => ReactionType::Like]);
                $message = 'رأی شما به لایک تغییر یافت.';
                $userReaction = 'like';
            }
        } else {
            $model->reactions()->create([
                'user_id' => $user->id,
                'type' => ReactionType::Like,
            ]);
            $message = 'لایک شما با موفقیت ثبت شد.';
            $userReaction = 'like';
        }

        return ApiResponse::success([
            'likes_count' => $model->likes()->count(),
            'dislikes_count' => $model->dislikes()->count(),
            'user_reaction' => $userReaction,
        ], $message);
    }

    #[OA\Post(
        path: "/api/v1/reactions/dislike",
        description: "Records a 'dislike' reaction from the authenticated user for a specific movie, series, or comment.",
        summary: "Submit a dislike reaction",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            description: "The type and ID of the resource to dislike",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/DislikeReactionRequest"),
        ),
        tags: ["Reactions"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_OK),
                        new OA\Property(property: "message", type: "string", example: "دیس‌لایک شما با موفقیت ثبت شد."),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: "Not found",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_NOT_FOUND),
                        new OA\Property(property: "message", type: "string", example: "موردی با این مشخصات یافت نشد.", nullable: true),
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
        ],
    )]
    public function dislike(DislikeReactionRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $model = $modelClass::findOrFail($id);

        $reaction = $model->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($reaction) {
            if ($reaction->type === ReactionType::Dislike) {
                $reaction->delete();
                $message = 'دیس‌لایک شما حذف شد.';
                $userReaction = null;
            } else {
                $reaction->update(['type' => ReactionType::Dislike]);
                $message = 'رأی شما به دیس‌لایک تغییر یافت.';
                $userReaction = 'dislike';
            }
        } else {
            $model->reactions()->create([
                'user_id' => $user->id,
                'type' => ReactionType::Dislike,
            ]);
            $message = 'دیس‌لایک شما با موفقیت ثبت شد.';
            $userReaction = 'dislike';
        }

        return ApiResponse::success([
            'likes_count' => $model->likes()->count(),
            'dislikes_count' => $model->dislikes()->count(),
            'user_reaction' => $userReaction,
        ], $message);
    }
}
