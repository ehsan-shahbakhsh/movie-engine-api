<?php

namespace App\Models;

use Database\Factories\AgeRatingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class AgeRating extends Model
{
    /** @use HasFactory<AgeRatingFactory> */
    use HasFactory;

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class);
    }
}
