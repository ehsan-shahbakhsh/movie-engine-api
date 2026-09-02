<?php

namespace App\Exceptions\Auth;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class InvalidCredentialsException extends Exception
{
    public function render(Request $request): ApiResponse
    {
        return ApiResponse::unauthorized(__('auth.failed'));
    }
}
