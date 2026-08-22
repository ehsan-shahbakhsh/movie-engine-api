<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\VerifyEmailController;

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware('signed')
    ->name('verification.verify');

Route::get('/', function () {
    return view('welcome');
});
