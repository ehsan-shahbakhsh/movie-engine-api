<?php

namespace App\Queries\Series;

use App\Enums\SeriesPublishStatus;
use App\Models\Series;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetSeriesListQuery
{
    public function execute(?string $search, int $page = 1): LengthAwarePaginator
    {
        $columns = [
            'id', 'title', 'original_title', 'slug',
            'release_year', 'release_date', 'end_date',
            'publish_status', 'production_status',
            'original_language_id', 'age_rating_id',
        ];

        $relations = [
            'media' => static fn($query) => $query->where('collection_name', 'poster'),
            'ageRating',
            'originalLanguage',
            'countries',
            'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
        ];

        if (blank($search)) {
            return Series::query()
                ->select($columns)
                ->with($relations)
                ->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon])
                ->latest()
                ->paginate(page: $page);
        }

        return Series::search($search)
            ->query(static fn($query) => $query->select($columns)->with($relations))
            ->paginate(page: $page);
    }
}
