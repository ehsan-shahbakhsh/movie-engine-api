<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UserResource",
    title: "UserResource",
    description: "User resource",
    required: ["id", "name", "email", "is_verified", "created_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Ehsan"),
        new OA\Property(property: "email", type: "string", format: "email", example: "ehsan.shahbakhsh.email@gmail.com"),
        new OA\Property(property: "is_verified", type: "boolean", example: false),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-01T00:00:00Z"),
    ],
)]
class UserResource extends JsonResource
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
            'email' => $this->email,
            'is_verified' => $this->email_verified_at !== null,
            'created_at' => $this->created_at,
        ];
    }
}
