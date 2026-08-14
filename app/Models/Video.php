<?php

namespace App\Models;

use App\Enums\VideoType;
use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'videoable_id',
    'videoable_type',
    'name',
    'type',
    'is_official',
    'is_active',
    'sort_order',
])]
class Video extends Model implements HasMedia
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;
    use InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

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

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(640)
            ->height(360)
            ->format('webp')
            ->sharpen(10)
            ->performOnCollections('thumbnail')
            ->queued();
    }
}
