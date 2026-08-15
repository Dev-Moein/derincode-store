<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Health Check
    |--------------------------------------------------------------------------
    */

    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is healthy.',
            'data' => [
                'version' => 'v1',
            ],
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Public Routes
        |--------------------------------------------------------------------------
        */

        Route::post('/register', [
            AuthController::class,
            'register',
        ]);

        Route::post('/login', [
            AuthController::class,
            'login',
        ]);

        Route::post('/forgot-password', [
            AuthController::class,
            'forgotPassword',
        ]);

        Route::post('/verify-otp', [
            AuthController::class,
            'verifyOtp',
        ]);

        Route::post('/reset-password', [
            AuthController::class,
            'resetPassword',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Protected Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:sanctum')->group(function () {

            Route::get('/me', [
                AuthController::class,
                'me',
            ]);

            Route::post('/logout', [
                AuthController::class,
                'logout',
            ]);
        });
    });
});
