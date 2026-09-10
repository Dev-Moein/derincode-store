<?php

use App\Http\Middleware\CheckPermission;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


return Application::configure(
    basePath: dirname(__DIR__)
)
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);

        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*')) {
                return null;
            }

            return route('login');
        });
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | 422 - Validation Error
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            ValidationException $e,
            $request
        ) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'Validation failed.',
                status: 422,
                errors: $e->errors(),
            );
        });

        /*
        |--------------------------------------------------------------------------
        | 401 - Unauthenticated
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            AuthenticationException $e,
            $request
        ) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'Unauthenticated.',
                status: 401,
            );
        });
            /*
|--------------------------------------------------------------------------
| 403 - Forbidden
|--------------------------------------------------------------------------
*/

$exceptions->render(function (
    AuthorizationException $e,
    $request
) {
    if (! $request->is('api/*')) {
        return null;
    }

    return ApiResponse::error(
        message: 'You do not have permission to perform this action.',
        status: 403,
    );
});
        /*
        |--------------------------------------------------------------------------
        | 404 - Resource Not Found
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            NotFoundHttpException $e,
            $request
        ) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'Resource not found.',
                status: 404,
            );
        });

        /*
        |--------------------------------------------------------------------------
        | 429 - Too Many Requests
        |--------------------------------------------------------------------------
        */

        $exceptions->render(function (
            ThrottleRequestsException $e,
            $request
        ) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiResponse::error(
                message: 'Too many requests. Please try again later.',
                status: 429,
            );
        });
        /*
|--------------------------------------------------------------------------
| 500 - Server Error
|--------------------------------------------------------------------------
*/

$exceptions->render(function (
    Throwable $e,
    $request
) {

    if (! $request->is('api/*')) {
        return null;
    }


    return ApiResponse::error(
        message: app()->environment('production')
            ? 'Something went wrong.'
            : $e->getMessage(),

        status: 500,
    );

});
    })

    ->create();
