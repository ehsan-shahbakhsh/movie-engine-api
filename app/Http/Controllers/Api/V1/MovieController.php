<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MovieStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/movies",
        description: "Returns a paginated list of all movies",
        summary: "Get list of movies",
        tags: ["Movies"],
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
                example: "Inception",
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
                            new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/MovieResource")),
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
        $movies = Movie::query()
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
            ])
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->when($request->filled('search'), static fn($query) => $query->where(static function ($query) use ($request) {
                $search = $request->search;
                $query->where('title', 'like', "%$search%")
                    ->orWhere('original_title', 'like', "%$search%");
            }))
            ->latest()
            ->paginate();

        return ApiResponse::success($movies->toResourceCollection());
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: "/api/v1/movies/{slug}",
        description: "Returns a single movie with full details including media",
        summary: "Get movie by SLUG",
        tags: ["Movies"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                description: "Movie SLUG",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string"),
                example: "inception-2010",
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
                        new OA\Property(property: "data", ref: "#/components/schemas/MovieResource"),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
            new OA\Response(
                response: Response::HTTP_NOT_FOUND,
                description: "Movie not found",
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
    public function show(string $slug)
    {
        $movie = Movie::query()
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
                'downloadGroups' => static fn($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'downloadGroups.downloadLinks' => static fn($query) => $query->with(['quality', 'encoder', 'codec', 'media']),
            ])
            ->where('slug', $slug)
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->firstOrFail();

        return ApiResponse::success($movie->toResource()->withMedia());
    }
}
