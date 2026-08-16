<?php

namespace App\Models;

use App\Enums\PersonSeriesDepartment;
use Database\Factories\PersonSeriesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['person_id', 'series_id', 'department', 'job', 'character_name'])]
class PersonSeries extends Pivot
{
    /** @use HasFactory<PersonSeriesFactory> */
    use HasFactory;

    protected $casts = [
        'department' => PersonSeriesDepartment::class,
    ];
}
