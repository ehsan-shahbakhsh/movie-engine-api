<?php

namespace App\Models;

use Database\Factories\SeasonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['series_id', 'season_number', 'title', 'release_date', 'end_date'])]
class Season extends Model
{
    /** @use HasFactory<SeasonFactory> */
    use HasFactory;

    protected $casts = [
        'season_number' => 'integer',
        'release_date' => 'date',
        'end_date' => 'date',
    ];
}
