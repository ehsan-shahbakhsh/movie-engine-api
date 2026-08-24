<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreWatchlistRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class WatchlistController extends Controller
{
    private const array MORPH_MAP = [
        'movie' => Movie::class,
        'series' => Series::class,
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $watchlists = $request->user()
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
            ->paginate();

        return ApiResponse::success($watchlists->toResourceCollection());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWatchlistRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $modelClass::findOrFail($id);

        $watchlist = $user->watchlists()
            ->where('watchable_id', $id)
            ->where('watchable_type', $modelClass)
            ->first();

        if ($watchlist) {
            return ApiResponse::error(
                message: 'این آیتم از قبل در لیست تماشای شما وجود دارد.',
                code: Response::HTTP_CONFLICT,
            );
        }

        $user->watchlists()->create([
            'watchable_id' => $id,
            'watchable_type' => $modelClass,
        ]);

        return ApiResponse::created(message: 'آیتم به لیست تماشا اضافه شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Watchlist $watchlist)
    {
        Gate::authorize('delete', $watchlist);

        $watchlist->delete();

        return ApiResponse::deleted('آیتم با موفقیت از لیست تماشا حذف شد.');
    }
}
