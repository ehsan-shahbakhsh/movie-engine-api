<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "GenreResource",
    title: "GenreResource",
    description: "Genre resource",
    required: ["id", "name", "slug"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Comedy"),
        new OA\Property(property: "slug", type: "string", example: "comedy"),
        new OA\Property(property: "description", type: "string", example: "A genre of comedic films.", nullable: true),
        new OA\Property(property: "movies_count", type: "integer", example: 10),
        new OA\Property(property: "series_count", type: "integer", example: 10),
    ],
)]
class GenreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),

            'movies_count' => $this->whenCounted('movies'),
            'series_count' => $this->whenCounted('series'),
        ];
    }
}
