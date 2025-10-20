<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleRequest;
use App\Models\User;
use App\Services\UserTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SocialiteController extends Controller
{
    public function getSocialiteToken(Request $request)
    {
        $userAgent = $request->header('User-Agent');
        $token = Crypt::encryptString((string) $userAgent);
        $keyToken = 'google_token_'.str()->uuid();
        Cache::put($keyToken, $token, 10 * 60);

        Crypt::decryptString(Cache::get($keyToken));

        return [
            'token' => $keyToken,
        ];
    }

    public function googleSignIn(GoogleRequest $request)
    {
        $socialiteToken = $request->header('SOCIALITE-TOKEN');
        $userAgent = $request->header('User-Agent');

        $token = Cache::get($socialiteToken);
        abort_if(! $token, 403, 'Socialite token is invalid. Please try again');

        $data = Crypt::decryptString($token);
        abort_if($userAgent != $data, 403, 'Socialite token is invalid. Please try again.');

        $user = User::firstOrCreate([
            'email' => $request->email,
        ], [
            'google_id' => $request->sub,
            'name' => $request->name,
            'email_verified_at' => now(),
            'status' => true,
        ]);

        abort_if($user->status != true, 404, 'Your account is not active. Please contact support if you believe this is a mistake.');

        $token = UserTokenService::getTokens($user);

        // delete cache
        Cache::forget($socialiteToken);

        return [
            'data' => $user,
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'],
        ];
    }
}
