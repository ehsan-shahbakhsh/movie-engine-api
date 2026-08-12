<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PersonResource",
    title: "PersonResource",
    description: "Person resource",
    required: ["id", "name", "original_name", "slug", "profile"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "کریستوفر نولان"),
        new OA\Property(property: "original_name", type: "string", example: "Christopher Nolan", nullable: true),
        new OA\Property(property: "slug", type: "string", example: "christopher-nolan"),
        new OA\Property(property: "profile", properties: [
            new OA\Property(property: "original", type: "string", example: "https://example.com/storage/1/profile.jpg"),
            new OA\Property(property: "thumb", type: "string", example: "https://example.com/storage/1/conversions/profile-thumb.webp"),
            new OA\Property(property: "medium", type: "string", example: "https://example.com/storage/1/conversions/profile-medium.webp"),
        ], type: "object", nullable: true),
        new OA\Property(property: "department", type: "string", example: "directing", enum: [
            "acting",
            "directing",
            "writing",
            "production",
            "camera",
            "editing",
            "sound",
            "art",
            "visual_effects",
            "music",
        ]),
        new OA\Property(property: "job", type: "string", example: "Director", nullable: true),
        new OA\Property(property: "character_name", type: "string", example: "Batman", nullable: true),
        new OA\Property(property: "birth_date", type: "string", format: "date", example: "1970-07-30"),
        new OA\Property(property: "death_date", type: "string", format: "date", example: "2026-01-01", nullable: true),
        new OA\Property(
            property: "biography",
            type: "string",
            example: "سر کریستوفر ادوارد نولان (زاده ۳۰ ژوئیه ۱۹۷۰) کارگردان، فیلم‌نامه‌نویس و تهیه‌کننده برجسته بریتانیایی-آمریکایی است. او با ساخت آثار پیچیده ذهنی، ساختارشکنی در خط زمانی و استفاده کمتر از جلوه‌های ویژه کامپیوتری، به یکی از تأثیرگذارترین و پرفروش‌ترین فیلم‌سازان تاریخ سینما تبدیل شده است.",
            nullable: true,
        ),
    ],
)]
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

            'department' => $this->whenPivotLoaded('movie_person', fn() => $this->pivot->department),
            'job' => $this->whenPivotLoaded('movie_person', fn() => $this->pivot->job),
            'character_name' => $this->whenPivotLoaded('movie_person', fn() => $this->pivot->character_name),

            'birth_date' => $this->whenHas('birth_date'),
            'death_date' => $this->whenHas('death_date'),
            'biography' => $this->whenHas('biography'),
        ];
    }
}
