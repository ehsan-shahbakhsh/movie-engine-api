<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'original_name' => $this->original_name,
            'slug' => $this->slug,

            'profile' => $this->when($this->relationLoaded('media'), function () {
                $profile = $this->getFirstMedia('profile');

                return [
                    'original' => $profile->getFullUrl(),
                    'thumb' => $profile->getFullUrl('thumb'),
                    'medium' => $profile->getFullUrl('medium'),
                ];
            }),

            'department' => $this->whenPivotLoaded('movie_person', fn() => $this->pivot->department->getLabel()),
            'job' => $this->whenPivotLoaded('movie_person', fn() => $this->pivot->job),
            'character_name' => $this->whenPivotLoaded('movie_person', fn() => $this->pivot->character_name),

            'birth_date' => $this->whenHas('birth_date'),
            'death_date' => $this->whenHas('death_date'),
            'biography' => $this->whenHas('biography'),
        ];
    }
}
