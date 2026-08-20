<?php

namespace App\Models;

use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['season_id', 'episode_number', 'title', 'synopsis', 'air_date', 'duration_minutes'])]
class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory;

    protected $casts = [
        'episode_number' => 'integer',
        'air_date' => 'date',
        'duration_minutes' => 'integer',
    ];

    public function downloadGroups(): MorphMany
    {
        return $this->morphMany(DownloadGroup::class, 'downloadable');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
