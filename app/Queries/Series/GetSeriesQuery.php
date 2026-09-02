<?php

namespace App\Queries\Series;

use App\Enums\SeriesPublishStatus;
use App\Models\Series;
use App\Models\User;

final class GetSeriesQuery
{
    public function execute(string $slug, ?User $user): Series
    {
        $series = Series::query()
            ->with([
                'media',
                'ageRating',
                'originalLanguage',
                'languages',
                'countries',
                'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
                'persons' => static fn($query) => $query->select(['people.id', 'name', 'original_name', 'slug'])->with('media'),
                'videos' => static fn($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'videos.media',
                'seasons.episodes.downloadGroups.downloadLinks' => static fn($query) => $query->with(['quality', 'encoder', 'codec', 'media']),
            ])
            ->withCount(['likes', 'dislikes'])
            ->where('slug', $slug)
            ->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon])
            ->firstOrFail();

        $series->user_reaction = $user
            ? $series->reactions()->where('user_id', $user->id)->value('type')
            : null;

        return $series;
    }
}
