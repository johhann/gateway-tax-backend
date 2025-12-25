<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\UserTokenService;
use App\Services\UserVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request): ?array
    {
        $user = User::query()
            ->where('email', $request->email)
            ->where('role', UserRole::USER)
            ->first();

        $errorMessage = 'Unable to sign in. Check your email & password and try again.';

        abort_if(! $user, 403, $errorMessage);
        abort_if(! $user->status, 403, $errorMessage);
        abort_if(! Hash::check($request->password, $user->password), 403, $errorMessage);

        UserVerificationService::send($user);

        return [
            'message' => 'Please check your email to verify your account.',
            'token' => UserTokenService::getVerificationToken($user),
        ];
    }

    public function logout(): array
    {
        UserTokenService::deleteTokens();

        return ['message' => 'Logged out successfully.'];
    }

    public function verify(Request $request): array
    {
        $user = User::find(Auth::id());
        UserVerificationService::verify($request);
        UserTokenService::deleteTokens();

        // mark user as verified
        if (! $user->email_verified_at) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        }

        return [
            'message' => 'User verified successfully.',
            'user' => $user,
            ...UserTokenService::getTokens($user),
        ];
    }

    public function resendVerification(Request $request): array
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::query()
            ->where('email', $request->email)
            ->first();

        abort_if(! $user, 404, 'User not found.');

        UserVerificationService::send($user);

        return [
            'message' => 'Please check your email to verify your account.',
            'token' => UserTokenService::getVerificationToken($user),
        ];
    }

    public function refreshToken()
    {
        return UserTokenService::getTokens(Auth::user());
    }
}
