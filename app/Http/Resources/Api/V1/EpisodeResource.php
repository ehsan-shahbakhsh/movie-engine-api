<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "EpisodeResource",
    title: "EpisodeResource",
    description: "Episode resource",
    required: [
        "id",
        "episode_number",
        "title",
        "synopsis",
        "air_date",
        "duration_minutes",
    ],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "episode_number", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "قسمت اول", nullable: true),
        new OA\Property(property: "synopsis", type: "string", example: "در این قسمت، شخصیت‌های اصلی با چالشی غیرمنتظره روبرو می‌شوند و...", nullable: true),
        new OA\Property(property: "air_date", type: "string", format: "date", example: "2011-06-19", nullable: true),
        new OA\Property(property: "duration_minutes", type: "integer", example: 45, nullable: true),
        new OA\Property(property: "download_groups", type: "array", items: new OA\Items(ref: "#/components/schemas/DownloadGroupResource")),
    ],
)]
class EpisodeResource extends JsonResource
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
            'episode_number' => $this->episode_number,
            'title' => $this->title,
            'synopsis' => $this->synopsis,
            'air_date' => $this->air_date?->toDateString(),
            'duration_minutes' => $this->duration_minutes,
            'download_groups' => DownloadGroupResource::collection($this->whenLoaded('downloadGroups')),
        ];
    }
}
