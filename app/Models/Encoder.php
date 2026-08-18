<?php

namespace App\Models;

use Database\Factories\EncoderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Encoder extends Model
{
    /** @use HasFactory<EncoderFactory> */
    use HasFactory;
}
