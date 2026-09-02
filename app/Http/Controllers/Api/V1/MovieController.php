<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Queries\Movie\GetMovieQuery;
use App\Queries\Movie\GetMoviesQuery;
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
    public function index(Request $request, GetMoviesQuery $query)
    {
        $page = $request->input('page');

        $movies = $query->execute(
            $request->input('search'),
            filter_var($page, FILTER_VALIDATE_INT) !== false ? (int)$page : 1,
        );

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
    public function show(Request $request, string $slug, GetMovieQuery $query)
    {
        $movie = $query->execute($slug, $request->user('sanctum'));

        return ApiResponse::success($movie->toResource()->withMedia());
    }
}
