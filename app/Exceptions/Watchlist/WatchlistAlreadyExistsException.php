<?php

namespace App\Exceptions\Watchlist;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WatchlistAlreadyExistsException extends Exception
{
    public function render(Request $request): ApiResponse
    {
        return ApiResponse::error(
            message: 'این آیتم از قبل در لیست تماشای شما وجود دارد.',
            code: Response::HTTP_CONFLICT,
        );
    }
}
