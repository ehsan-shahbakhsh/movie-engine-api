<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "VideoResource",
    title: "VideoResource",
    description: "Video resource",
    required: ["id", "name", "type", "is_official", "thumbnail", "video"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Official Trailer"),
        new OA\Property(property: "type", type: "string", example: "trailer", enum: ["trailer", "teaser", "clip", "featurette", "promotional"]),
        new OA\Property(property: "is_official", type: "boolean", example: true),
        new OA\Property(property: "thumbnail", type: "string", example: "https://example.com/storage/1/conversions/thumb.webp", nullable: true),
        new OA\Property(property: "video", type: "string", example: "https://example.com/storage/1/video.mp4", nullable: true),
    ],
)]
class VideoResource extends JsonResource
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
            'type' => $this->type,
            'is_official' => $this->is_official,
            'thumbnail' => $this->whenLoaded(
                'media',
                fn() => $this->getFirstMedia('thumbnail')?->getFullUrl('thumb'),
                null,
            ),
            'video' => $this->whenLoaded(
                'media',
                fn() => $this->getFirstMedia('video')?->getFullUrl(),
                null,
            ),
        ];
    }
}
