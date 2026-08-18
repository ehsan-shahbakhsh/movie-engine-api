<?php

namespace App\Models;

use Database\Factories\DownloadGroupFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['downloadable_id', 'downloadable_type', 'title', 'sort_order', 'is_active'])]
class DownloadGroup extends Model
{
    /** @use HasFactory<DownloadGroupFactory> */
    use HasFactory;

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
