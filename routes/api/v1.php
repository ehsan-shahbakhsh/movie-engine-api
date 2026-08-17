<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{MovieController, GenreController, PersonController, SeriesController};

Route::get('movies', [MovieController::class, 'index']);
Route::get('movies/{movie:slug}', [MovieController::class, 'show']);

Route::get('genres', GenreController::class);

Route::get('persons', PersonController::class);

Route::get('series', [SeriesController::class, 'index']);
Route::get('series/{series:slug}', [SeriesController::class, 'show']);
