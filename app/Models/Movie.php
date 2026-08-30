<?php

namespace App\Models;

use App\Enums\MovieStatus;
use App\Enums\ReactionType;
use App\Http\Resources\Api\V1\MovieCollection;
use App\Http\Resources\Api\V1\MovieResource;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Attributes\UseResourceCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[UseResource(MovieResource::class)]
#[UseResourceCollection(MovieCollection::class)]
#[Fillable([
    'title',
    'original_title',
    'slug',
    'synopsis',
    'release_year',
    'release_date',
    'duration_minutes',
    'age_rating_id',
    'original_language_id',
    'status',
])]
class Movie extends Model implements HasMedia
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory;
    use Sluggable;
    use InteractsWithMedia;

    protected $casts = [
        'release_year' => 'integer',
        'release_date' => 'date',
        'duration_minutes' => 'integer',
        'status' => MovieStatus::class,
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function ageRating(): BelongsTo
    {
        return $this->belongsTo(AgeRating::class);
    }

    public function originalLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'original_language_id');
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
            ->using(MoviePerson::class)
            ->withPivot(['department', 'job', 'character_name'])
            ->withTimestamps();
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(Video::class, 'videoable');
    }

    public function downloadGroups(): MorphMany
    {
        return $this->morphMany(DownloadGroup::class, 'downloadable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function likes(): MorphMany
    {
        return $this->reactions()->where('type', ReactionType::Like);
    }

    public function dislikes(): MorphMany
    {
        return $this->reactions()->where('type', ReactionType::Dislike);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('poster')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this
            ->addMediaCollection('backdrop')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this
            ->addMediaCollection('logo')
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

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug_source'
            ]
        ];
    }

    protected function slugSource(): Attribute
    {
        return Attribute::make(
            get: static fn($value, array $attributes) => $attributes['original_title'] ?? $attributes['title'],
        );
    }
}
