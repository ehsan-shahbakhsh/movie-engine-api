<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "WatchlistResource",
    title: "WatchlistResource",
    description: "Watchlist resource",
    required: [
        "id",
        "type",
        "created_at",
    ],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "type", type: "string", example: "movie", enum: ["movie", "series"]),
        new OA\Property(property: "item", oneOf: [
            new OA\Schema(ref: "#/components/schemas/MovieResource"),
            new OA\Schema(ref: "#/components/schemas/SeriesResource"),
        ]),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-01T00:00:00Z"),
    ],
)]
class WatchlistResource extends JsonResource
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
            'type' => match ($this->watchable_type) {
                Movie::class => 'movie',
                Series::class => 'series',
            },
            'item' => $this->whenLoaded('watchable', fn() => $this->watchable->toResource()),
            'created_at' => $this->created_at,
        ];
    }
}
