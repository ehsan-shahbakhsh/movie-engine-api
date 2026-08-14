<?php

namespace App\Models;

use App\Enums\VideoType;
use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'videoable_id',
    'videoable_type',
    'name',
    'type',
    'is_official',
    'is_active',
    'sort_order',
])]
class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    protected $casts = [
        'type' => VideoType::class,
        'is_official' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function videoable(): MorphTo
    {
        return $this->morphTo();
    }
}
