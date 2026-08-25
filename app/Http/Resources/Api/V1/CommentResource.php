<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
