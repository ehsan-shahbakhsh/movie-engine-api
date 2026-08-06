<?php

namespace App\Models;

use App\Enums\MovieStatus;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

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
        'status',
    ];

    protected $casts = [
        'release_year' => 'integer',
        'release_date' => 'date',
        'duration_minutes' => 'integer',
        'status' => MovieStatus::class,
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
