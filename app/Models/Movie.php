<?php

namespace App\Models;

use App\Enums\MovieStatus;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory;
    use Sluggable;

    protected $fillable = [
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
    ];

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
