<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Auth Routes
 */
Route::prefix('v1/auth/')
    ->group(function () {
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('resend-verification', [AuthController::class, 'resendVerification'])->name('resend-verification');
        Route::post('register', [RegisterController::class, 'store'])->name('users.register');

        Route::post('forgot-password', [ForgetPasswordController::class, 'initiate']);
        Route::post('forgot-password/reset', [ForgetPasswordController::class, 'handle']);

        // Social Login
        Route::get('get-socialite-token', [SocialiteController::class, 'getSocialiteToken'])->name('users.device');
        Route::post('google-signin', [SocialiteController::class, 'googleSignIn'])->name('users.google-signin');

        Route::post('verify', [AuthController::class, 'verify'])
            ->middleware(['auth:sanctum', 'ability:code-verify'])
            ->name('users.verify');

        Route::get('refresh-token', [AuthController::class, 'refreshToken'])
            ->middleware(['auth:sanctum', 'ability:issue-access-token']);

        Route::middleware(['auth:sanctum', 'ability:access-token'])
            ->group(function () {
                Route::get('profile', [UserController::class, 'profile'])->name('users.profile');
                Route::delete('logout', [AuthController::class, 'logout'])->name('logout');
                Route::post('change-password', [NewPasswordController::class, 'handle']);
            });
    });
