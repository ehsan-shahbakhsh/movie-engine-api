<?php

namespace App\Actions\Auth;

use App\Models\User;

final class LogoutAction
{
    public function execute(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
