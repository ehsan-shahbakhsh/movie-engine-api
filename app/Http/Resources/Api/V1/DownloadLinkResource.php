<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "DownloadLinkResource",
    title: "DownloadLinkResource",
    description: "Download link resource",
    required: ["id", "quality", "encoder", "codec", "size_in_bytes", "human_readable_size", "video"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "quality", type: "string", example: "1080p", nullable: true),
        new OA\Property(property: "encoder", type: "string", example: "Tigole", nullable: true),
        new OA\Property(property: "codec", type: "string", example: "x264", nullable: true),
        new OA\Property(property: "size_in_bytes", type: "integer", example: 1_610_612_736, nullable: true),
        new OA\Property(property: "human_readable_size", type: "integer", example: "1.5 GB", nullable: true),
        new OA\Property(property: "video", type: "string", example: "https://example.com/storage/1/video.mp4", nullable: true),
    ],
)]
class DownloadLinkResource extends JsonResource
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

            'quality' => $this->whenLoaded('quality', fn() => $this->quality->name, null),
            'encoder' => $this->whenLoaded('encoder', fn() => $this->encoder?->name, null),
            'codec' => $this->whenLoaded('codec', fn() => $this->codec?->name, null),

            $this->mergeWhen($this->relationLoaded('media'), function () {
                $video = $this->getFirstMedia('video');

                return [
                    'size_in_bytes' => $video?->size,
                    'human_readable_size' => $video?->human_readable_size,
                    'video' => $video?->getFullUrl(),
                ];
            }, [
                'size_in_bytes' => null,
                'human_readable_size' => null,
                'video' => null,
            ]),
        ];
    }
}
