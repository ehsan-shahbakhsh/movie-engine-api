<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CommentStatus;
use App\Enums\MovieStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreMovieCommentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class MovieCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/movies/{slug}/comments",
        description: "Returns a paginated list of comments for the specified movie.",
        summary: "Get movie comments",
        tags: ["Comments"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string"),
            ),
            new OA\Parameter(
                name: "page",
                description: "Page number",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1, minimum: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_OK),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(property: "data", required: ["items", "pagination"], properties: [
                            new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/CommentResource")),
                            new OA\Property(property: "pagination", required: [
                                "current_page",
                                "from",
                                "last_page",
                                "per_page",
                                "to",
                                "total",
                                "has_more",
                            ], properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 5),
                                new OA\Property(property: "from", type: "integer", example: 61),
                                new OA\Property(property: "last_page", type: "integer", example: 10),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "to", type: "integer", example: 75),
                                new OA\Property(property: "total", type: "integer", example: 150),
                                new OA\Property(property: "has_more", type: "boolean", example: true),
                            ], type: "object"),
                        ]),
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
        ],
    )]
    public function index(string $slug)
    {
        $movie = Movie::query()
            ->where('slug', $slug)
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->firstOrFail();

        $comments = $movie
            ->comments()
            ->with(['user', 'allApprovedReplies'])
            ->whereNull('parent_id')
            ->where('status', CommentStatus::Approved)
            ->latest()
            ->paginate();

        return ApiResponse::success($comments->toResourceCollection());
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: "/api/v1/movies/{slug}/comments",
        description: "Creates a new comment for the specified movie.",
        summary: "Create a movie comment",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            description: "The comment data to be stored",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StoreMovieCommentRequest"),
        ),
        tags: ["Comments"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_CREATED),
                        new OA\Property(property: "message", type: "string", example: "نظر شما با موفقیت ثبت شد و پس از تأیید مدیر نمایش داده خواهد شد."),
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
                response: Response::HTTP_FORBIDDEN,
                description: "Forbidden",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_FORBIDDEN),
                        new OA\Property(property: "message", type: "string", example: "Your email address is not verified.", nullable: true),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ]
                )
            ),
        ],
    )]
    public function store(StoreMovieCommentRequest $request, string $slug)
    {
        $movie = Movie::query()
            ->where('slug', $slug)
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->firstOrFail();

        $validated = $request->validated();

        $movie->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'parent_id' => $validated['reply_to'] ?? null,
            'is_spoiler' => $validated['is_spoiler'],
        ]);

        return ApiResponse::created(message: 'نظر شما با موفقیت ثبت شد و پس از تأیید مدیر نمایش داده خواهد شد.');
    }
}
