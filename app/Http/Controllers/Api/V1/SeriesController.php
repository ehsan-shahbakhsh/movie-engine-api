<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SeriesPublishStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Series;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/series",
        description: "Returns a paginated list of all series",
        summary: "Get list of series",
        tags: ["Series"],
        parameters: [
            new OA\Parameter(
                name: "page",
                description: "Page number",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1, minimum: 1),
            ),
            new OA\Parameter(
                name: "search",
                description: "Search term",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string"),
                example: "Game of Thrones",
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
                            new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/SeriesResource")),
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
        ],
    )]
    public function index(Request $request)
    {
        $series = Series::query()
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
            ])
            ->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon])
            ->when($request->filled('search'), static fn($query) => $query->where(static function ($query) use ($request) {
                $search = $request->search;
                $query->where('title', 'like', "%$search%")
                    ->orWhere('original_title', 'like', "%$search%");
            }))
            ->latest()
            ->paginate();

        return ApiResponse::success($series->toResourceCollection());
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: "/api/v1/series/{slug}",
        description: "Returns a single series with full details including media",
        summary: "Get series by SLUG",
        tags: ["Series"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                description: "Series SLUG",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "game-of-thrones-2011",
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
                        new OA\Property(property: "data", ref: "#/components/schemas/SeriesResource"),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: "Series not found",
                content: new OA\JsonContent(
                    required: ["success", "code", "message", "data", "meta", "errors", "error_code"],
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "code", type: "integer", example: Response::HTTP_NOT_FOUND),
                        new OA\Property(property: "message", type: "string", example: "موردی با این مشخصات یافت نشد."),
                        new OA\Property(property: "data", type: "object", nullable: true),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
        ],
    )]
    public function show(Request $request, string $slug)
    {
        $series = Series::query()
            ->with([
                'media',
                'ageRating',
                'originalLanguage',
                'languages',
                'countries',
                'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
                'persons' => static fn($query) => $query->select(['people.id', 'name', 'original_name', 'slug'])->with('media'),
                'videos' => static fn($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'videos.media',
                'seasons.episodes.downloadGroups.downloadLinks' => static fn($query) => $query->with(['quality', 'encoder', 'codec', 'media']),
            ])
            ->withCount(['likes', 'dislikes'])
            ->where('slug', $slug)
            ->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon])
            ->firstOrFail();

        $series->user_reaction = $request->user('sanctum')
            ? $series->reactions()->where('user_id', $request->user()->id)->value('type')
            : null;

        return ApiResponse::success($series->toResource()->withMedia());
    }
}
