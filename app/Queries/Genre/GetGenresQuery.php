<?php

namespace App\Queries\Genre;

use App\Enums\MovieStatus;
use App\Enums\SeriesPublishStatus;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;

final class GetGenresQuery
{
    public function execute(): Collection
    {
        return Genre::query()
            ->where('is_active', true)
            ->withCount([
                'movies' => static function ($query) {
                    $query->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon]);
                },
                'series' => static function ($query) {
                    $query->whereIn('publish_status', [SeriesPublishStatus::Published, SeriesPublishStatus::ComingSoon]);
                },
            ])
            ->orderBy('sort_order')
            ->get();
    }
}
