<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\App\Controllers\AuthController;
use Modules\Auth\App\Controllers\EmailVerificationController;
use Modules\Auth\App\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Auth module — API routes
|--------------------------------------------------------------------------
|
| This file is required from routes/api.php. Laravel wraps it with:
| Route::middleware('api')->prefix('api')->group(...)
|
| Final URLs: /api/student/register, /api/auth/login, etc.
|
*/

// Role-specific registration: /api/{role}/register
Route::post('student/register', [AuthController::class, 'registerStudent']);
Route::post('company/register', [AuthController::class, 'registerCompany']);
Route::post('mentor/register', [AuthController::class, 'registerMentor']);

// Shared auth: /api/auth/*
Route::prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword']);

    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('email/resend', [EmailVerificationController::class, 'resend']);
    });
});
