<?php

namespace App\Models;

use App\Enums\SeriesProductionStatus;
use App\Enums\SeriesPublishStatus;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\SeriesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'title',
    'original_title',
    'slug',
    'synopsis',
    'release_year',
    'release_date',
    'end_date',
    'age_rating_id',
    'original_language_id',
    'publish_status',
    'production_status',
])]
class Series extends Model
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;
    use Sluggable;

    protected $casts = [
        'release_year' => 'integer',
        'release_date' => 'date',
        'end_date' => 'date',
        'publish_status' => SeriesPublishStatus::class,
        'production_status' => SeriesProductionStatus::class,
    ];

    public function ageRating(): BelongsTo
    {
        return $this->belongsTo(AgeRating::class);
    }

    public function originalLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'original_language_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug_source',
            ],
        ];
    }

    protected function slugSource(): Attribute
    {
        return Attribute::make(
            get: static fn($value, array $attributes) => $attributes['original_title'] ?? $attributes['title'],
        );
    }
}
