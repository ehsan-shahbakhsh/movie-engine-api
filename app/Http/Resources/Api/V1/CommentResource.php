<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "CommentResource",
    title: "CommentResource",
    description: "Comment resource",
    required: ["id", "body", "is_official", "is_spoiler", "replies", "created_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_name", type: "string", example: "Ehsan"),
        new OA\Property(property: "body", type: "string", example: "این فیلم فوق‌العاده بود!"),
        new OA\Property(property: "is_official", type: "boolean", example: false),
        new OA\Property(property: "is_spoiler", type: "boolean", example: false),
        new OA\Property(property: "replies", type: "array", items: new OA\Items(ref: "#/components/schemas/CommentResource")),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-01T00:00:00Z"),
    ],
)]
class CommentResource extends JsonResource
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

            'user_name' => $this->whenLoaded('user', fn() => $this->user->name),

            'body' => $this->body,

            'is_official' => $this->is_official,
            'is_spoiler' => $this->is_spoiler,

            'replies' => CommentResource::collection($this->whenLoaded('allApprovedReplies', default: [])),

            'created_at' => $this->created_at,
        ];
    }
}
