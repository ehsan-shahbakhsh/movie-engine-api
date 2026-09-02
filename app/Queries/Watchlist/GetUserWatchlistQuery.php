<?php

namespace App\Queries\Watchlist;

use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetUserWatchlistQuery
{
    public function execute(User $user, int $page = 1): LengthAwarePaginator
    {
        return $user
            ->watchlists()
            ->with([
                'watchable' => static function ($morphTo) {
                    $morphTo->constrain([
                        Movie::class => static function ($query) {
                            $query
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
                                ]);
                        },
                        Series::class => static function ($query) {
                            $query
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
                                ]);
                        },
                    ]);
                },
            ])
            ->latest()
            ->paginate(page: $page);
    }
}
