<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreWatchlistRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class WatchlistController extends Controller
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
    ];

    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/watchlists",
        description: "Returns a paginated list of the authenticated user's watchlist items.",
        summary: "Get list of watchlist items",
        security: [["sanctum" => []]],
        tags: ["Watchlists"],
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
                            new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/WatchlistResource")),
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
    public function index(Request $request)
    {
        $watchlists = $request->user()
            ->watchlists()
            ->with([
                'watchable' => static function ($morphTo) {
                    $morphTo->constrain([
                        Movie::class => static function ($query) {
                            $query
                                ->select([
                                    'id',
                                    'title',
                                    'original_title',
                                    'slug',
                                    'release_year',
                                    'release_date',
                                    'duration_minutes',
                                    'status',
                                    'original_language_id',
                                    'age_rating_id',
                                ])
                                ->with([
                                    'media' => static fn($query) => $query->where('collection_name', 'poster'),
                                    'ageRating',
                                    'originalLanguage',
                                    'countries',
                                    'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
                                ]);
                        },
                        Series::class => static function ($query) {
                            $query
                                ->select([
                                    'id',
                                    'title',
                                    'original_title',
                                    'slug',
                                    'release_year',
                                    'release_date',
                                    'end_date',
                                    'publish_status',
                                    'production_status',
                                    'original_language_id',
                                    'age_rating_id',
                                ])
                                ->with([
                                    'media' => static fn($query) => $query->where('collection_name', 'poster'),
                                    'ageRating',
                                    'originalLanguage',
                                    'countries',
                                    'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
                                ]);
                        },
                    ]);
                },
            ])
            ->latest()
            ->paginate();

        return ApiResponse::success($watchlists->toResourceCollection());
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: "/api/v1/watchlists",
        description: "Adds a movie or series to the authenticated user's watchlist.",
        summary: "Add item to watchlist",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            description: "The type and ID of the item to add to watchlist",
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StoreWatchlistRequest"),
        ),
        tags: ["Watchlists"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Successful operation",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_CREATED),
                        new OA\Property(property: "message", type: "string", example: "آیتم به لیست تماشا اضافه شد."),
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
                        new OA\Property(property: "message", type: "string", example: "این آیتم از قبل در لیست تماشای شما وجود دارد.", nullable: true),
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
    public function store(StoreWatchlistRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $modelClass::findOrFail($id);

        $watchlist = $user->watchlists()
            ->where('watchable_id', $id)
            ->where('watchable_type', $modelClass)
            ->first();

        if ($watchlist) {
            return ApiResponse::error(
                message: 'این آیتم از قبل در لیست تماشای شما وجود دارد.',
                code: Response::HTTP_CONFLICT,
            );
        }

        $user->watchlists()->create([
            'watchable_id' => $id,
            'watchable_type' => $modelClass,
        ]);

        return ApiResponse::created(message: 'آیتم به لیست تماشا اضافه شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/v1/watchlists/{watchlist}",
        description: "Removes a specific item from the authenticated user's watchlist.",
        summary: "Remove item from watchlist",
        security: [["sanctum" => []]],
        tags: ["Watchlists"],
        parameters: [
            new OA\Parameter(name: "watchlist", in: "path", required: true, schema: new OA\Schema(type: "integer")),
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
                        new OA\Property(property: "message", type: "string", example: "آیتم با موفقیت از لیست تماشا حذف شد."),
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
    public function destroy(Watchlist $watchlist)
    {
        Gate::authorize('delete', $watchlist);

        $watchlist->delete();

        return ApiResponse::deleted('آیتم با موفقیت از لیست تماشا حذف شد.');
    }
}
