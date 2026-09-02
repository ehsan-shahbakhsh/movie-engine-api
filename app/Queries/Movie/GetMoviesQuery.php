<?php

namespace App\Queries\Movie;

use App\Enums\MovieStatus;
use App\Models\Movie;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetMoviesQuery
{
    public function execute(?string $search, int $page = 1): LengthAwarePaginator
    {
        return Movie::query()
            ->select([
                'id',
                'title',
                'original_title',
                'slug',
                'release_year',
                'release_date',
                'duration_minutes',
                'status',
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
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->when($search != null, static fn($query) => $query->where(static function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('original_title', 'like', "%$search%");
            }))
            ->latest()
            ->paginate(page: $page);
    }
}
