<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

class ResetPasswordService
{
    public static function generate(User $user): string
    {
        $uuid = str()->uuid();
        $expiresAt = now()->addMinutes(30)->timestamp;
        $uniqueKey = $user->email.'-'.$expiresAt.'-'.$uuid;

        Cache::put('forget_'.$user->id, $uniqueKey, 30 * 60);

        $data = [
            'user_id' => $user->id,
            'expires_at' => $expiresAt,
            'email' => $user->email,
            'unique_key' => $uuid,
        ];

        $code = Crypt::encryptString(json_encode($data));

        $key = 'forget_'.str()->uuid().'_'.now()->timestamp;

        Cache::put($key, $code, 30 * 60);

        return URL::temporarySignedRoute('users.forget-password', now()->addMinutes(30), ['code' => $key]);
    }

    public static function verify(string $key): mixed
    {
        $code = Cache::get($key);

        if (! $code) {
            return redirect(config('frontend.registration_url').'?success=false&type=user_verification_error');
        }

        $data = json_decode(Crypt::decryptString($code), true);

        $uniqueKey = Cache::get('forget_'.$data['user_id']);

        if (! $uniqueKey) {
            return redirect(config('frontend.registration_url').'?success=false&type=forget_verification_error');
        }

        $expiresAt = $data['expires_at'] ?? null;

        if (! $expiresAt || $expiresAt < time()) {
            return redirect(config('frontend.registration_url').'?success=false&type=user_verification_error');
        }

        return $code;
    }
}
