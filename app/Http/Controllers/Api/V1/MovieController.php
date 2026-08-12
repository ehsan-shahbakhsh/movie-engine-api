<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MovieStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $movies = Movie::query()
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
            ->when($request->filled('search'), static fn($query) => $query->where(static function ($query) use ($request) {
                $search = $request->search;
                $query->where('title', 'like', "%$search%")
                    ->orWhere('original_title', 'like', "%$search%");
            }))
            ->latest()
            ->paginate();

        return ApiResponse::success($movies->toResourceCollection());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
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
            ])
            ->where('slug', $slug)
            ->whereIn('status', [MovieStatus::Published, MovieStatus::ComingSoon])
            ->firstOrFail();;

        return ApiResponse::success($movie->toResource());
    }
}
