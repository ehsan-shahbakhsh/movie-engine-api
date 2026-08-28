<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    MovieController,
    GenreController,
    PersonController,
    SeriesController,
    RegisterController,
    LoginController,
    MeController,
    LogoutController,
    FavoriteController,
    WatchlistController,
    MovieCommentController,
    SeriesCommentController,
    CommentController,
};

Route::get('movies', [MovieController::class, 'index']);
Route::get('movies/{movie:slug}', [MovieController::class, 'show']);

Route::get('genres', GenreController::class);

Route::get('persons', PersonController::class);

Route::get('series', [SeriesController::class, 'index']);
Route::get('series/{series:slug}', [SeriesController::class, 'show']);

Route::prefix('auth')->group(function () {
    Route::post('register', RegisterController::class);
    Route::post('login', LoginController::class); // TODO: add rate limit

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', MeController::class);
        Route::post('logout', LogoutController::class);
    });
});

Route::get('movies/{movie:slug}/comments', [MovieCommentController::class, 'index']);
Route::get('series/{series:slug}/comments', [SeriesCommentController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
    Route::apiResource('watchlists', WatchlistController::class)->only(['index', 'store', 'destroy']);

    Route::middleware('verified')->group(function () {
        Route::post('movies/{movie:slug}/comments', [MovieCommentController::class, 'store']);
        Route::post('series/{series:slug}/comments', [SeriesCommentController::class, 'store']);
        Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
    });
});
