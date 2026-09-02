<?php

namespace App\Queries\Series;

use App\Enums\SeriesPublishStatus;
use App\Models\Series;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetSeriesListQuery
{
    public function execute(?string $search, int $page = 1): LengthAwarePaginator
    {
        return Series::query()
            ->select([
                'id',
                'title',
                'original_title',
                'slug',
                'release_year',
                'release_date',
                'end_date',
                'publish_status',
                'production_status',
                'original_language_id',
                'age_rating_id',
            ])
            ->with([
                'media' => static fn($query) => $query->where('collection_name', 'poster'),
                'ageRating',
                'originalLanguage',
                'countries',
                'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
            ])
            ->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon])
            ->when($search != null, static fn($query) => $query->where(static function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('original_title', 'like', "%$search%");
            }))
            ->latest()
            ->paginate(page: $page);
    }
}
