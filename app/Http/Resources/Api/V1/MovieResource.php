<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "MovieResource",
    title: "MovieResource",
    description: "Movie resource",
    required: [
        "id",
        "title",
        "original_title",
        "slug",
        "poster",
        "release_year",
        "release_date",
        "duration_minutes",
        "status",
        "original_language",
        "age_rating",
        "countries",
        "genres",
    ],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "تلقین"),
        new OA\Property(property: "original_title", type: "string", example: "Inception", nullable: true),
        new OA\Property(property: "slug", type: "string", example: "inception-2010"),

        new OA\Property(property: "poster", properties: [
            new OA\Property(property: "original", type: "string", example: "https://example.com/storage/1/poster.jpg"),
            new OA\Property(property: "thumb", type: "string", example: "https://example.com/storage/1/conversions/poster-thumb.webp"),
            new OA\Property(property: "medium", type: "string", example: "https://example.com/storage/1/conversions/poster-medium.webp"),
        ], type: "object", nullable: true),
        new OA\Property(
            property: "synopsis",
            type: "string",
            example: "یک دزد که رازهای تجاری را از طریق فناوری به اشتراک‌گذاری خواب می‌دزدد، ماموریت معکوسی دریافت می‌کند...",
            nullable: true,
        ),
        new OA\Property(property: "release_year", type: "integer", example: 2010),
        new OA\Property(property: "release_date", type: "string", format: "date", example: "2010-07-16", nullable: true),
        new OA\Property(property: "duration_minutes", type: "integer", example: 148, nullable: true),
        new OA\Property(property: "status", type: "string", example: "published", enum: ["published", "coming_soon"]),

        new OA\Property(property: "original_language", type: "string", example: "English", nullable: true),
        new OA\Property(property: "languages", type: "array", items: new OA\Items(type: "string"), example: ["English", "Japanese"]),
        new OA\Property(property: "age_rating", type: "string", example: "PG-13", nullable: true),
        new OA\Property(property: "countries", type: "array", items: new OA\Items(type: "string"), example: ["United States", "United Kingdom"]),

        new OA\Property(property: "genres", type: "array", items: new OA\Items(ref: "#/components/schemas/GenreResource")),
        new OA\Property(property: "persons", type: "array", items: new OA\Items(ref: "#/components/schemas/PersonResource")),

        new OA\Property(property: "backdrop", type: "string", example: "https://example.com/storage/1/conversions/backdrop.webp", nullable: true),
        new OA\Property(property: "logo", type: "string", example: "https://example.com/storage/1/logo.png", nullable: true),
        new OA\Property(
            property: "gallery",
            type: "array",
            items: new OA\Items(type: "string", format: "uri"),
            example: [
                "https://example.com/storage/1/gallery-1.png",
                "https://example.com/storage/1/gallery-2.png",
            ],
        ),
    ],
)]
class MovieResource extends JsonResource
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
            'duration_minutes' => $this->duration_minutes,
            'status' => $this->status,

            'original_language' => $this->whenLoaded('originalLanguage', fn() => $this->originalLanguage->name, null),
            'languages' => $this->whenLoaded('languages', fn() => $this->languages->pluck('name')),
            'age_rating' => $this->whenLoaded('ageRating', fn() => $this->ageRating->name, null),
            'countries' => $this->whenLoaded('countries', fn() => $this->countries->pluck('name'), []),

            'genres' => GenreResource::collection($this->whenLoaded('genres', default: [])),
            'persons' => PersonResource::collection($this->whenLoaded('persons')),

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
