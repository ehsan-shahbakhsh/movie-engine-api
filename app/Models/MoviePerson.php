<?php

namespace App\Models;

use App\Enums\MoviePersonDepartment;
use Database\Factories\MoviePersonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['movie_id', 'person_id', 'department', 'job', 'character_name'])]
class MoviePerson extends Pivot
{
    /** @use HasFactory<MoviePersonFactory> */
    use HasFactory;

    protected $casts = [
        'department' => MoviePersonDepartment::class,
    ];
}
