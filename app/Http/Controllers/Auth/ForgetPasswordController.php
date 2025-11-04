<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\UserTokenService;
use App\Services\UserVerificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends Controller
{
    public function initiate(ForgetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        abort_if(! $user, 404, 'User not found');

        UserVerificationService::send($user);

        return [
            'message' => 'Reset password verification code has been sent to your email.',
            'time' => now(),
        ];
    }

    public function handle(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        abort_if(! $user, 404, 'User not found');

        UserVerificationService::verify($request, $user->id);

        $user->password = Hash::make($request['new_password']);
        $user->save();

        if ($request->log_out_other_devices) {
            $user->tokens()->delete();
        }

        Cache::forget($request->token);

        return [
            'message' => 'Password reset successfully.',
            'user' => $user,
            ...UserTokenService::getTokens($user),
        ];
    }
}
