<?php

namespace App\Models;

use Database\Factories\CodecFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Codec extends Model
{
    /** @use HasFactory<CodecFactory> */
    use HasFactory;
}
