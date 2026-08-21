<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DownloadController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ProjectRequestController;
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
    | Projects
    |--------------------------------------------------------------------------
    */

    Route::prefix('projects')->group(function () {

        /*
        | Public
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
        | Admin
        */

        Route::middleware([
            'auth:sanctum',
        ])->group(function () {

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

            Route::put('/{slug}/images/reorder', [
                ProjectController::class,
                'reorderImages',
            ])->middleware('permission:projects.update');

            Route::delete('/images/{image}', [
                ProjectController::class,
                'destroyImage',
            ])->middleware('permission:projects.update');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Project Requests
    |--------------------------------------------------------------------------
    */

    Route::prefix('project-requests')->group(function () {

        /*
        | Customer
        */

        Route::middleware([
            'auth:sanctum',
        ])->group(function () {

            Route::get('/', [
                ProjectRequestController::class,
                'index',
            ])->middleware('permission:project-requests.view');

            Route::post('/', [
                ProjectRequestController::class,
                'store',
            ])->middleware('permission:project-requests.view');
        });

        /*
        | Admin
        */

        Route::middleware([
            'auth:sanctum',
            'permission:project-requests.manage',
        ])->group(function () {

            Route::get('/admin', [
                ProjectRequestController::class,
                'adminIndex',
            ]);

            Route::get('/admin/{id}', [
                ProjectRequestController::class,
                'show',
            ]);

            Route::put('/admin/{id}/status', [
                ProjectRequestController::class,
                'updateStatus',
            ]);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    Route::prefix('payments')
        ->middleware('auth:sanctum')
        ->group(function () {

            Route::get('/', [
                PaymentController::class,
                'index',
            ]);

            Route::post('/', [
                PaymentController::class,
                'store',
            ]);

            Route::get('/{id}', [
                PaymentController::class,
                'show',
            ]);
        });

    /*
    |--------------------------------------------------------------------------
    | Payment Callback
    |--------------------------------------------------------------------------
    |
    | Public route because Zarinpal redirects the user here.
    |
    */

    Route::get('/payments/{payment}/callback', [
        PaymentController::class,
        'callback',
    ])->name('payments.callback');

    /*
    |--------------------------------------------------------------------------
    | Downloads
    |--------------------------------------------------------------------------
    |
    | Authenticated customers can download projects they have successfully
    | purchased.
    |
    */

    Route::prefix('downloads')
        ->middleware('auth:sanctum')
        ->group(function () {

            Route::get('/projects/{project}', [
                DownloadController::class,
                'download',
            ])->name('downloads.project');

            Route::get('/', [
                DownloadController::class,
                'index',
            ])->name('downloads.index');
        });
});
