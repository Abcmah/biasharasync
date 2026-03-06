<?php

use Illuminate\Support\Facades\Route;

// PDF download (public, no auth required)
Route::get('v1/pdf/{uniqueId}/{lang?}', [\App\Http\Controllers\Api\AuthController::class, 'pdf'])
    ->name('api.extra.pdf');

Route::prefix('v1/auth')->middleware('throttle:password-reset')->group(function () {
    Route::post('forgot-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'forgotPassword']);
    Route::post('reset-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'resetPassword']);
});

Route::prefix('v1/auth')->middleware('throttle:10,1')->group(function () {
    Route::post('google', [\App\Http\Controllers\Api\GoogleAuthController::class, 'login']);
});

// Onboarding routes
Route::prefix('v1/onboarding')->group(function () {
    Route::get('plans', [\App\Http\Controllers\Api\OnboardingController::class, 'plans']);
});

Route::prefix('v1/onboarding')->middleware(['api.auth.check', 'throttle:onboarding'])->group(function () {
    Route::post('company', [\App\Http\Controllers\Api\OnboardingController::class, 'createCompany']);
});

// Blog comment API (public, throttled)
Route::post('v1/blog/{slug}/comment', [\App\Http\Controllers\Api\BlogCommentController::class, 'store'])
    ->middleware('throttle:10,1');

// Blog comment API (public, throttled)
Route::post('v1/blog/{slug}/comment', [\App\Http\Controllers\Api\BlogCommentController::class, 'store'])
    ->middleware('throttle:10,1');
