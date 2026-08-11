<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MovieController;

Route::get('movies', [MovieController::class, 'index']);
Route::get('movies/{movie:slug}', [MovieController::class, 'show']);
