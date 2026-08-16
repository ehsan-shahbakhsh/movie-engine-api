<?php

namespace App\Models;

use App\Http\Resources\Api\V1\PersonCollection;
use App\Http\Resources\Api\V1\PersonResource;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Attributes\UseResourceCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[UseResource(PersonResource::class)]
#[UseResourceCollection(PersonCollection::class)]
#[Fillable(['name', 'original_name', 'slug', 'birth_date', 'death_date', 'biography'])]
class Person extends Model implements HasMedia
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory;
    use Sluggable;
    use InteractsWithMedia;

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
    ];

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class)
            ->using(MoviePerson::class)
            ->withPivot(['department', 'job', 'character_name'])
            ->withTimestamps();
    }

    public function series(): BelongsToMany
    {
        return $this->belongsToMany(Series::class)
            ->using(PersonSeries::class)
            ->withPivot(['department', 'job', 'character_name'])
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('profile')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->format('webp')
            ->sharpen(10)
            ->performOnCollections('profile')
            ->queued();

        $this
            ->addMediaConversion('medium')
            ->width(500)
            ->height(500)
            ->format('webp')
            ->performOnCollections('profile')
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
                'source' => 'slug_source',
            ],
        ];
    }

    protected function slugSource(): Attribute
    {
        return Attribute::make(
            get: static fn($value, array $attributes) => $attributes['original_name'] ?? $attributes['name'],
        );
    }
}
