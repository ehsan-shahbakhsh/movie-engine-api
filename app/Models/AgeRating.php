<?php

namespace App\Models;

use Database\Factories\AgeRatingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class AgeRating extends Model
{
    /** @use HasFactory<AgeRatingFactory> */
    use HasFactory;
}
