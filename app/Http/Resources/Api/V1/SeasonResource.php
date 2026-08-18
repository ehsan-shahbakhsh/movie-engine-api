<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "SeasonResource",
    title: "SeasonResource",
    description: "Season resource",
    required: [
        "id",
        "season_number",
        "title",
        "release_date",
        "end_date",
    ],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "season_number", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "فصل اول", nullable: true),
        new OA\Property(property: "release_date", type: "string", format: "date", example: "2011-04-17", nullable: true),
        new OA\Property(property: "end_date", type: "string", format: "date", example: "2011-06-19", nullable: true),
    ],
)]
class SeasonResource extends JsonResource
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
            'season_number' => $this->season_number,
            'title' => $this->title,
            'release_date' => $this->release_date,
            'end_date' => $this->end_date,
        ];
    }
}
