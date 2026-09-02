<?php

namespace App\Data\Auth;

use App\Models\User;
use Spatie\LaravelData\Data;

class AuthResultData extends Data
{
    public function __construct(
        public readonly User              $user,
        public readonly AuthorizationData $authorization,
    )
    {
    }
}
