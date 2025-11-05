<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\UserTokenService;
use App\Services\UserVerificationService;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(StoreUserRequest $request)
    {
        $unverifiedUserExists = User::where('email', $request->email)
            ->whereNotNull('email_verified_at')
            ->exists();

        abort_if($unverifiedUserExists, 422, 'Email already exists');

        $user = User::updateOrCreate([
            'email' => $request->email,
        ], values: [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'role' => UserRole::USER,
            'password' => Hash::make($request->password),
            'status' => true,
        ]);

        // save avatar
        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('user-avatar');
        }

        UserVerificationService::send($user);

        return [
            'message' => 'Please check your email to verify your account.',
            'token' => UserTokenService::getVerificationToken($user),
        ];
    }
}
