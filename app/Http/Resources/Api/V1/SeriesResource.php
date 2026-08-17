<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource
{
    public bool $showMedia = false;

    public function withMedia(): static
    {
        $this->showMedia = true;

        return $this;
    }

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
            'original_title' => $this->original_title,
            'slug' => $this->slug,

            'poster' => $this->when($this->relationLoaded('media') && $this->hasMedia('poster'), function () {
                $poster = $this->getFirstMedia('poster');

                return [
                    'original' => $poster->getFullUrl(),
                    'thumb' => $poster->getFullUrl('thumb'),
                    'medium' => $poster->getFullUrl('medium'),
                ];
            }, null),

            'synopsis' => $this->whenHas('synopsis'),

            'release_year' => $this->release_year,
            'release_date' => $this->release_date,
            'end_date' => $this->end_date,
            'publish_status' => $this->publish_status,
            'production_status' => $this->production_status,

            'original_language' => $this->whenLoaded('originalLanguage', fn() => $this->originalLanguage->name, null),
            'languages' => $this->whenLoaded('languages', fn() => $this->languages->pluck('name')),
            'age_rating' => $this->whenLoaded('ageRating', fn() => $this->ageRating->name, null),
            'countries' => $this->whenLoaded('countries', fn() => $this->countries->pluck('name'), []),

            'genres' => GenreResource::collection($this->whenLoaded('genres', default: [])),
            'persons' => PersonResource::collection($this->whenLoaded('persons')),
            'videos' => VideoResource::collection($this->whenLoaded('videos')),

            'backdrop' => $this->when(
                $this->relationLoaded('media') && $this->showMedia,
                fn() => $this->getFirstMedia('backdrop')?->getFullUrl('backdrop'),
            ),
            'logo' => $this->when(
                $this->relationLoaded('media') && $this->showMedia,
                fn() => $this->getFirstMedia('logo')?->getFullUrl(),
            ),
            'gallery' => $this->when(
                $this->relationLoaded('media') && $this->showMedia,
                fn() => $this->getMedia('gallery')->map(static fn($media) => $media->getFullUrl()),
            ),
        ];
    }
}
