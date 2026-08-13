<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Person;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/v1/persons",
        description: "Returns a paginated list of all persons",
        summary: "Get list of persons",
        tags: ["Persons"],
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
                example: "Christopher Nolan",
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
                            new OA\Property(property: "items", type: "array", items: new OA\Items(ref: "#/components/schemas/PersonResource")),
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
    public function __invoke(Request $request)
    {
        $persons = Person::query()
            ->with('media')
            ->when($request->filled('search'), static function ($query) use ($request) {
                $search = $request->search;
                $query->where('name', 'like', "%$search%")
                    ->orWhere('original_name', 'like', "%$search%");
            })
            ->paginate();

        return ApiResponse::success($persons->toResourceCollection());
    }
}
