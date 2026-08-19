<?php

namespace App\Models;

use Database\Factories\DownloadLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['download_group_id', 'quality_id', 'encoder_id', 'codec_id'])]
class DownloadLink extends Model
{
    /** @use HasFactory<DownloadLinkFactory> */
    use HasFactory;

    public function downloadGroup(): BelongsTo
    {
        return $this->belongsTo(DownloadGroup::class);
    }

    public function quality(): BelongsTo
    {
        return $this->belongsTo(Quality::class);
    }

    public function encoder(): BelongsTo
    {
        return $this->belongsTo(Encoder::class);
    }

    public function codec(): BelongsTo
    {
        return $this->belongsTo(Codec::class);
    }
}
