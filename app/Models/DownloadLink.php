<?php

namespace App\Models;

use Database\Factories\DownloadLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['download_group_id', 'quality_id', 'encoder_id', 'codec_id'])]
class DownloadLink extends Model implements HasMedia
{
    /** @use HasFactory<DownloadLinkFactory> */
    use HasFactory;
    use InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('video')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes([
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/quicktime',
                'video/x-msvideo',
                'video/x-matroska',
                'video/x-flv',
            ]);
    }
}
