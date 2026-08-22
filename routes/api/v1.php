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
    });
});
