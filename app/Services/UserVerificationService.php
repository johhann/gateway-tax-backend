<?php

namespace App\Services;

use App\Emails\SendVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

final class UserVerificationService
{
    public static function send(User $user)
    {
        $ttl = config('verify.ttl');
        $key = 'verify_'.$user->id;
        $code = fake()->randomNumber(4, true);

        Cache::put($key, $code, now()->addMinutes($ttl));

        Mail::to($user->email)->send(new SendVerificationCode($user, $code));

        return true;
    }

    public static function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:4',
        ]);

        $verificationCode = Cache::get('verify_'.Auth::id());

        abort_if(! $verificationCode, 403, 'Sorry, your verification code has expired. Please try again.');
        abort_if($verificationCode != $request->code, 403, 'Incorrect verification code. Please try again.');

        Cache::forget('verify_'.Auth::id());

        return true;
    }
}
