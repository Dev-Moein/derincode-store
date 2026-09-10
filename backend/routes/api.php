<?php

use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DownloadController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProfileController;
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
| Contact
|--------------------------------------------------------------------------
|
| Public endpoint for visitors to send a contact message.
|
*/

Route::post('/contact', [
    ContactController::class,
    'store',
]);



    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::prefix('auth')->group(function () {

        /*
        |----------------------------------------------------------------------
        | Guest
        |----------------------------------------------------------------------
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
        |----------------------------------------------------------------------
        | Authenticated User
        |----------------------------------------------------------------------
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
    | Projects
    |--------------------------------------------------------------------------
    */

    Route::prefix('projects')->group(function () {

        /*
        |----------------------------------------------------------------------
        | Public
        |----------------------------------------------------------------------
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
        |----------------------------------------------------------------------
        | Project Management
        |----------------------------------------------------------------------
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

            /*
            |------------------------------------------------------------------
            | Project Images
            |------------------------------------------------------------------
            */

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

    Route::prefix('project-requests')
        ->middleware('auth:sanctum')
        ->group(function () {

            /*
            |------------------------------------------------------------------
            | Customer
            |------------------------------------------------------------------
            */

            /*
            | User's own project requests.
            */

           Route::get('/', [
    ProjectRequestController::class,
    'index',
]);


Route::post('/', [
    ProjectRequestController::class,
    'store',
]);

            /*
            |------------------------------------------------------------------
            | Admin
            |------------------------------------------------------------------
            */

            Route::prefix('admin')
                ->middleware('permission:project-requests.manage')
                ->group(function () {

                    /*
                    | List all project requests.
                    */

                    Route::get('/', [
                        ProjectRequestController::class,
                        'adminIndex',
                    ]);

                    /*
                    | View a single project request.
                    */

                    Route::get('/{id}', [
                        ProjectRequestController::class,
                        'show',
                    ]);

                    /*
                    | Update project request status.
                    */

                    Route::put('/{id}/status', [
                        ProjectRequestController::class,
                        'updateStatus',
                    ]);
                });
        });
        Route::prefix('admin')
    ->middleware([
        'auth:sanctum',
        'permission:dashboard.view',
    ])
    ->group(function () {
        Route::get(
            '/dashboard',
            [AdminDashboardController::class, 'index']
        );
    });


    /*
|--------------------------------------------------------------------------
| Admin Users
|--------------------------------------------------------------------------
*/

Route::prefix('admin/users')
    ->middleware([
        'auth:sanctum',
        'permission:users.view',
    ])
    ->group(function () {

        Route::get('/', [
            AdminUserController::class,
            'index',
        ]);


        Route::get('/{id}', [
            AdminUserController::class,
            'show',
        ]);


        Route::put('/{id}/role', [
            AdminUserController::class,
            'updateRole',
        ])
        ->middleware('permission:users.update');


        Route::delete('/{id}', [
            AdminUserController::class,
            'destroy',
        ])
        ->middleware('permission:users.delete');

    });
    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    */

    Route::prefix('payments')
        ->middleware('auth:sanctum')
        ->group(function () {

            /*
            | User's payment history.
            */

            Route::get('/', [
                PaymentController::class,
                'index',
            ]);

            /*
            | Initiate a payment.
            */

            Route::post('/', [
                PaymentController::class,
                'store',
            ]);

            /*
            | View a specific payment.
            |
            | Ownership is validated inside the application layer.
            */

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
    | This route must remain public because the payment gateway redirects
    | the user back to this endpoint after the payment process.
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
    */

    Route::prefix('downloads')
        ->middleware('auth:sanctum')
        ->group(function () {

            /*
            | Download a successfully purchased project.
            |
            | Purchase ownership is validated inside DownloadService.
            */

            Route::get('/projects/{project}', [
                DownloadController::class,
                'download',
            ])->name('downloads.project');

            /*
            | Authenticated user's download history.
            */

            Route::get('/', [
                DownloadController::class,
                'index',
            ])->name('downloads.index');
        });
    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')
        ->middleware('auth:sanctum')
        ->group(function () {

            /*
            | Get authenticated user's profile.
            */

            Route::get('/', [
                ProfileController::class,
                'show',
            ]);

            /*
            | Update authenticated user's profile.
            */

            Route::put('/', [
                ProfileController::class,
                'update',
            ]);

            /*
            | Change authenticated user's password.
            */

            Route::put('/password', [
                ProfileController::class,
                'changePassword',
            ]);
        });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/dashboard', [
            DashboardController::class,
            'index',
        ]);
    });

});
