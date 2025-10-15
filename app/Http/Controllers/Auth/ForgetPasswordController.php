<?php

namespace App\Http\Controllers\Auth;

use App\Emails\ResetPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\ResetPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    public function initiate(ForgetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->whereNotNull('email_verified_at')->first();
        abort_if(! $user, 404, 'User not found');

        $verificationUrl = ResetPasswordService::generate($user);

        Mail::to($user->email)->send(new ResetPassword($user, $verificationUrl));

        return [
            'message' => 'Reset password email sent successfully',
            'time' => now(),
        ];
    }

    public function verify(Request $request, string $key)
    {
        if (! $request->hasValidSignature()) {
            return redirect(config('frontend.reset_url').'?success=false&type=user_verification_error');
        }

        $code = Cache::get($key);
        if (! $code) {
            return redirect(config('frontend.reset_url').'?success=false&type=user_verification_error');
        }

        $data = json_decode(Crypt::decryptString($code), true);

        $uniqueKey = Cache::get('forget_'.$data['user_id']);

        if (! $uniqueKey) {
            return redirect(config('frontend.reset_url').'?success=false&type=forget_verification_error');
        }

        $expiresAt = $data['expires_at'] ?? null;

        if (! $expiresAt || $expiresAt < time()) {
            return redirect(config('frontend.reset_url').'?success=false&type=forget_verification_error');
        }

        Cache::forget('forget_'.$data['user_id']);

        $key = 'reset_'.str()->uuid().'_'.now()->timestamp;
        Cache::put($key, $code, 30 * 60);

        return redirect(config('frontend.reset_url').'?success=true&type=forget_verification_success&token='.$key);
    }

    public function handle(ResetPasswordRequest $request)
    {
        $code = Cache::get($request->token);

        if (! $code) {
            return redirect(config('frontend.registration_url').'?success=false&type=forget_verification_error');
        }

        $data = json_decode(Crypt::decryptString($code), true);

        if (! $data) {
            return redirect(config('frontend.registration_url').'?success=false&type=forget_verification_error');
        }

        $user = User::find($data['user_id']);
        abort_if(! $user, 404, 'User not found');

        $user->password = Hash::make($request['new_password']);
        $user->save();

        if ($request->log_out_other_devices) {
            $user->tokens()->delete();
        }

        Cache::forget($request->token);

        return ['message' => 'User Password Reset Successfully'];
    }
}
