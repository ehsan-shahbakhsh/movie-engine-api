<?php

namespace App\Queries\Favorite;

use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

final class GetUserFavoritesQuery
{
    public function execute(User $user, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        return $user
            ->favorites()
            ->with([
                'favoritable' => static function ($morphTo) {
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
            ->paginate(perPage: $perPage, page: $page);
    }
}
