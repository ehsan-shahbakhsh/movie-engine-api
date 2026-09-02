<?php

namespace App\Queries\Movie;

use App\Enums\MovieStatus;
use App\Models\Movie;
use App\Models\User;

final class GetMovieQuery
{
    public function execute(string $slug, ?User $user): Movie
    {
        $movie = Movie::query()
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
                'downloadGroups' => static fn($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'downloadGroups.downloadLinks' => static fn($query) => $query->with(['quality', 'encoder', 'codec', 'media']),
            ])
            ->withCount(['likes', 'dislikes'])
            ->where('slug', $slug)
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->firstOrFail();

        $movie->user_reaction = $user
            ? $movie->reactions()->where('user_id', $user->id)->value('type')
            : null;

        return $movie;
    }
}
