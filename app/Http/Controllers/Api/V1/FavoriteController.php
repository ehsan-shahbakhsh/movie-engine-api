<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreFavoriteRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Favorite;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
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
        $favorites = $request->user()
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
            ->paginate();

        return ApiResponse::success($favorites->toResourceCollection());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFavoriteRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();
        $id = $validated['id'];
        $type = $validated['type'];
        $modelClass = static::MORPH_MAP[$type];

        $modelClass::findOrFail($id);

        $favorite = $user->favorites()
            ->where('favoritable_id', $id)
            ->where('favoritable_type', $modelClass)
            ->first();

        if ($favorite) {
            return ApiResponse::error(
                message: 'این آیتم از قبل در لیست علاقه‌مندی‌های شما وجود دارد.',
                code: Response::HTTP_CONFLICT,
            );
        }

        $user->favorites()->create([
            'favoritable_id' => $id,
            'favoritable_type' => $modelClass,
        ]);

        return ApiResponse::created(message: 'آیتم به لیست علاقه‌مندی‌ها اضافه شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        Gate::authorize('delete', $favorite);

        $favorite->delete();

        return ApiResponse::deleted('آیتم با موفقیت از لیست علاقه‌مندی‌ها حذف شد.');
    }
}
