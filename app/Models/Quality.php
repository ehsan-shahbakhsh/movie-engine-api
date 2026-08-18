<?php

namespace App\Models;

use Database\Factories\QualityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Quality extends Model
{
    /** @use HasFactory<QualityFactory> */
    use HasFactory;
}
