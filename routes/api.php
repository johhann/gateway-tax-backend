<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\IdentificationController;
use App\Http\Controllers\LegalCityController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\TaxRequestController;
use App\Http\Controllers\TaxStationController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Auth Routes
 */
Route::prefix('v1')->group(function () {
    Route::prefix('auth/')
        ->group(function () {
            Route::post('login', [AuthController::class, 'login'])->name('login');
            Route::post('resend-verification', [AuthController::class, 'resendVerification'])->name('resend-verification');
            Route::post('register', [RegisterController::class, 'store'])->name('users.register');

            Route::post('forgot-password', [ForgetPasswordController::class, 'initiate']);
            Route::post('forgot-password/reset', [ForgetPasswordController::class, 'handle'])
                ->middleware(['auth:sanctum', 'ability:access-token']);

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

    // STEP ONE
    Route::prefix('stepOne')->middleware(['auth:sanctum', 'ability:access-token'])->group(function () {
        Route::get('profile', [ProfileController::class, 'show']);
        Route::post('profile', [ProfileController::class, 'store']);
        Route::put('profile', [ProfileController::class, 'update']);
    });

    // STEP TWO
    Route::prefix('stepTwo')->middleware(['auth:sanctum', 'ability:access-token'])->group(function () {
        Route::post('identification', [IdentificationController::class, 'store']);
        Route::get('identification', [IdentificationController::class, 'show']);
        Route::put('identification', [IdentificationController::class, 'update']);
    });

    Route::prefix('stepThree')->middleware(['auth:sanctum', 'ability:access-token'])->group(function () {
        Route::post('business', [BusinessController::class, 'store']);
        Route::get('business', [BusinessController::class, 'show']);
        Route::put('business', [BusinessController::class, 'update']);
    });

    Route::prefix('stepFive')->middleware(['auth:sanctum', 'ability:access-token'])->group(function () {
        Route::get('legal', [LegalController::class, 'show']);
        Route::post('legal', [LegalController::class, 'store']);
        Route::put('legal', [LegalController::class, 'update']);
    });

    Route::prefix('stepSix')->middleware(['auth:sanctum', 'ability:access-token'])->group(function () {
        Route::get('payment', [PaymentController::class, 'show']);
        Route::post('payment', [PaymentController::class, 'store']);
        Route::put('payment', [PaymentController::class, 'update']);
    });

    Route::middleware(['auth:sanctum', 'ability:access-token'])->group(function () {
        Route::post('upload', [UploadController::class, 'store']);
        Route::delete('upload/{attachment}', [UploadController::class, 'destroy']);
        Route::get('/image/{attachment}', [UploadController::class, 'show']);
        Route::get('cities', [LegalCityController::class, '__invoke']);
        Route::get('tax-stations', [TaxStationController::class, '__invoke']);
        Route::get('summary', [SummaryController::class, '__invoke']);
        Route::post('tax-request', [TaxRequestController::class, 'store']);
        Route::get('schedule/{schedule}', [ScheduleController::class, 'show']);
        Route::get('schedule', [ScheduleController::class, 'index']);
        Route::post('schedule', [ScheduleController::class, 'store']);
        Route::put('schedule/{schedule}', [ScheduleController::class, 'update']);
    });

    /**
     * Notifications
     */
    Route::middleware(['auth:sanctum', 'ability:access-token'])
        ->group(function () {
            Route::get('notifications', [NotificationController::class, 'index']);
            Route::get('notifications-unread-counter', [NotificationController::class, 'counter']);
            Route::get('notifications-mark-all-read', [NotificationController::class, 'markAllRead']);
            Route::get('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
        });
});
