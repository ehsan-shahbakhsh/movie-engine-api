<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Favorite\CreateFavoriteAction;
use App\Actions\Favorite\DestroyFavoriteAction;
use App\Exceptions\Favorite\FavoriteAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFavoriteRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Favorite;
use App\Queries\Favorite\GetUserFavoritesQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/favorites",
        description: "Returns a paginated list of the authenticated user's favorite items.",
        summary: "Get list of favorites",
        security: [["sanctum" => []]],
        tags: ["Favorites"],
        parameters: [
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
                            new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/FavoriteResource")),
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
        ],
    )]
    public function index(Request $request, GetUserFavoritesQuery $query)
    {
        $page = $request->input('page');

        $favorites = $query->execute(
            $request->user(),
            filter_var($page, FILTER_VALIDATE_INT) !== false ? (int)$page : 1,
        );

        return ApiResponse::success($favorites->toResourceCollection());
    }

    /**
     * Store a newly created resource in storage.
     * @throws FavoriteAlreadyExistsException
     */
    #[OA\Post(
        path: "/api/v1/favorites",
        description: "Adds a movie or series to the authenticated user's favorites.",
        summary: "Add item to favorites",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            description: "The type and ID of the item to add to favorites",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StoreFavoriteRequest"),
        ),
        tags: ["Favorites"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_CREATED),
                        new OA\Property(property: "message", type: "string", example: "آیتم به لیست علاقه‌مندی‌ها اضافه شد."),
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
                response: Response::HTTP_CONFLICT,
                description: "Conflict",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_CONFLICT),
                        new OA\Property(property: "message", type: "string", example: "این آیتم از قبل در لیست علاقه‌مندی‌های شما وجود دارد.", nullable: true),
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
    public function store(StoreFavoriteRequest $request, CreateFavoriteAction $action)
    {
        $validated = $request->validated();
        $user = $request->user();

        $action->execute($user, $validated['id'], $validated['type']);

        return ApiResponse::created(message: 'آیتم به لیست علاقه‌مندی‌ها اضافه شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/v1/favorites/{favorite}",
        description: "Removes a specific item from the authenticated user's favorites.",
        summary: "Remove item from favorites",
        security: [["sanctum" => []]],
        tags: ["Favorites"],
        parameters: [
            new OA\Parameter(name: "favorite", in: "path", required: true, schema: new OA\Schema(type: "integer")),
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
                        new OA\Property(property: "message", type: "string", example: "آیتم با موفقیت از لیست علاقه‌مندی‌ها حذف شد."),
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
                response: Response::HTTP_FORBIDDEN,
                description: "Forbidden",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_FORBIDDEN),
                        new OA\Property(property: "message", type: "string", example: "شما دسترسی لازم برای انجام این عملیات را ندارید.", nullable: true),
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
        ],
    )]
    public function destroy(Favorite $favorite, DestroyFavoriteAction $action)
    {
        Gate::authorize('delete', $favorite);

        $action->execute($favorite);

        return ApiResponse::deleted('آیتم با موفقیت از لیست علاقه‌مندی‌ها حذف شد.');
    }
}
