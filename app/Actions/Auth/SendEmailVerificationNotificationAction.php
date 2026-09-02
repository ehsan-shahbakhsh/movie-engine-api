<?php

namespace App\Actions\Auth;

use App\Exceptions\Auth\EmailAlreadyVerifiedException;
use App\Models\User;

final class SendEmailVerificationNotificationAction
{
    /**
     * @throws EmailAlreadyVerifiedException
     */
    public function execute(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException;
        }

        $user->sendEmailVerificationNotification();
    }
}
