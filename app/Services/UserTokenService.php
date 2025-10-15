<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserTokenService
{
    public static function getVerificationToken(User $user): string
    {
        $verificationTokenExpiration = now()->addMinutes((int) config('sanctum.verification_token'));
        $verificationToken = $user->createToken('access_token', ['code-verify'], $verificationTokenExpiration);

        return $verificationToken->plainTextToken;
    }

    public static function getTokens(User $user, bool $remember = false): array
    {
        self::deleteTokens();

        if ($remember) {
            $accessTokenExpiration = now()->addDays(7);
            $refreshTokenExpiration = now()->addDays(9);
        } else {
            $refreshTokenExpiration = now()->addMinutes((int) config('sanctum.refresh_token'));
            $accessTokenExpiration = now()->addMinutes((int) config('sanctum.access_token'));
        }

        $accessToken = $user->createToken('access_token', ['access-token'], $accessTokenExpiration);
        $refreshToken = $user->createToken('refresh_token', ['issue-access-token'], $refreshTokenExpiration);

        return [
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
        ];
    }

    public static function deleteTokens(?User $user = null, bool $all = true): void
    {
        if (! $user) {
            $user = User::find(Auth::id());
        }

        if ($all) {
            $user->tokens()->forceDelete();

            return;
        }

        $user->currentAccessToken()->forceDelete();
    }
}
