<?php

namespace App\Queries\Movie;

use App\Enums\MovieStatus;
use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetMoviesQuery
{
    public function execute(?string $search, int $page = 1): LengthAwarePaginator
    {
        $columns = [
            'id', 'title', 'original_title', 'slug',
            'release_year', 'release_date', 'duration_minutes',
            'status', 'original_language_id', 'age_rating_id',
        ];

        $relations = [
            'media' => static fn($query) => $query->where('collection_name', 'poster'),
            'ageRating',
            'originalLanguage',
            'countries',
            'genres' => static fn($query) => $query->select(['id', 'name', 'slug'])->where('is_active', true),
        ];

        if (blank($search)) {
            return Movie::query()
                ->select($columns)
                ->with($relations)
                ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
                ->latest()
                ->paginate(page: $page);
        }

        return Movie::search($search)
            ->query(static fn($query) => $query->select($columns)->with($relations))
            ->paginate(page: $page);
    }
}
