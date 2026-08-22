<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id, string $hash)
    {
        $user = User::query()->find($id);

        if (!$user) {
            abort(Response::HTTP_NOT_FOUND, 'کاربر مورد نظر یافت نشد.');
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->route('hash'))) {
            abort(Response::HTTP_FORBIDDEN, 'لینک تأیید ایمیل نامعتبر است.');
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect('/'); // TODO: create email verified view
    }
}
