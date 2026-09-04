<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Queries\Genre\GetGenresQuery;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/genres",
        description: "Returns a list of all genres",
        summary: "Get list of genres",
        tags: ["Genres"],
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
                        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/GenreResource")),
                        new OA\Property(property: "meta", type: "object", nullable: true),
                        new OA\Property(property: "errors", type: "object", nullable: true),
                        new OA\Property(property: "error_code", type: "string", nullable: true),
                    ],
                ),
            ),
        ],
    )]
    public function __invoke(GetGenresQuery $query)
    {
        $genres = $query->execute();

        return ApiResponse::success($genres->toResourceCollection());
    }
}
