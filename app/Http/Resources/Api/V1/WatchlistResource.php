<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
