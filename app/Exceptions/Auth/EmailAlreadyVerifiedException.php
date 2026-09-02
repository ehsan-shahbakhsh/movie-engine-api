<?php

namespace App\Exceptions\Auth;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class EmailAlreadyVerifiedException extends Exception
{
    public function render(Request $request): ApiResponse
    {
        return ApiResponse::badRequest('ایمیل شما قبلاً تایید شده است.');
    }
}
