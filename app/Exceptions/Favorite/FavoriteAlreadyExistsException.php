<?php

namespace App\Exceptions\Favorite;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FavoriteAlreadyExistsException extends Exception
{
    public function render(Request $request): ApiResponse
    {
        return ApiResponse::error(
            message: 'این آیتم از قبل در لیست علاقه‌مندی‌های شما وجود دارد.',
            code: Response::HTTP_CONFLICT,
        );
    }
}
