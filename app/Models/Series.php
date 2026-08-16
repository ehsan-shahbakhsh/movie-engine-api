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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
class Series extends Model implements HasMedia
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;
    use Sluggable;
    use InteractsWithMedia;

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

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }

    public function countries(): BelongsToMany
    {
        return $this->belongsToMany(Country::class);
    }

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)
            ->using(PersonSeries::class)
            ->withPivot(['department', 'job', 'character_name'])
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('poster')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('backdrop')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('logo')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('gallery')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(450)
            ->format('webp')
            ->sharpen(10)
            ->performOnCollections('poster')
            ->queued();

        $this->addMediaConversion('medium')
            ->width(500)
            ->height(750)
            ->format('webp')
            ->performOnCollections('poster')
            ->queued();

        $this->addMediaConversion('backdrop')
            ->width(1920)
            ->height(1080)
            ->format('webp')
            ->performOnCollections('backdrop')
            ->queued();
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
