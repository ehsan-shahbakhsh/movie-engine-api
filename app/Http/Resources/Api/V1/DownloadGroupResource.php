<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "DownloadGroupResource",
    title: "DownloadGroupResource",
    description: "Download group resource",
    required: ["id", "title"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "دوبله فارسی"),
        new OA\Property(property: "download_links", type: "array", items: new OA\Items(ref: "#/components/schemas/DownloadLinkResource")),
    ],
)]
class DownloadGroupResource extends JsonResource
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
            'title' => $this->title,

            'download_links' => DownloadLinkResource::collection($this->whenLoaded('downloadLinks')),
        ];
    }
}
