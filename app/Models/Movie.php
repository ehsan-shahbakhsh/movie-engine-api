<?php

namespace App\Models;

use App\Enums\MovieStatus;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory;

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
}
