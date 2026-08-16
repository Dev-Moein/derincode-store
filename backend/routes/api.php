<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
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
        ])->middleware('throttle:login');

        Route::post('/forgot-password', [
            AuthController::class,
            'forgotPassword',
        ])->middleware('throttle:forgot-password');

        Route::post('/verify-otp', [
            AuthController::class,
            'verifyOtp',
        ])->middleware('throttle:verify-otp');

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

    /*
    |--------------------------------------------------------------------------
    | Project Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('projects')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Public Routes
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            ProjectController::class,
            'index',
        ]);

        Route::get('/{slug}', [
            ProjectController::class,
            'show',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Protected Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:sanctum')->group(function () {

            Route::post('/', [
                ProjectController::class,
                'store',
            ])->middleware('permission:projects.create');

            Route::put('/{slug}', [
                ProjectController::class,
                'update',
            ])->middleware('permission:projects.update');

            Route::delete('/{slug}', [
                ProjectController::class,
                'destroy',
            ])->middleware('permission:projects.delete');

            Route::post('/{slug}/images', [
                ProjectController::class,
                'storeImage',
            ])->middleware('permission:projects.update');

            Route::delete('/images/{image}', [
                ProjectController::class,
                'destroyImage',
            ])->middleware('permission:projects.update');
        });
    });
});
