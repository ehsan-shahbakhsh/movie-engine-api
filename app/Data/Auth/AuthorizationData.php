<?php

namespace App\Data\Auth;

use Carbon\Carbon;
use Spatie\LaravelData\Data;

class AuthorizationData extends Data
{
    public function __construct(
        public readonly string  $accessToken,
        public readonly string  $tokenType,
        public readonly ?int    $expiresIn,
        public readonly ?Carbon $expiresAt,
    )
    {
    }
}
