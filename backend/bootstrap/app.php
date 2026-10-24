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
                message: 'اطلاعات واردشده معتبر نیست. لطفاً موارد مشخص‌شده را بررسی کنید.',
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
                message: 'برای انجام این عملیات باید وارد حساب کاربری شوید.',
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
                message: 'شما اجازه انجام این عملیات را ندارید.',
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
                message: 'مورد درخواستی پیدا نشد.',
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
                message: 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.',
                status: 429,
            );
        });
        /*
|--------------------------------------------------------------------------
| 409 - Project Cannot Be Deleted
|--------------------------------------------------------------------------
*/

        $exceptions->render(function (
            RuntimeException $e,
            $request
        ) {
            if (! $request->is('api/*')) {
                return null;
            }

            if (
                $e->getMessage() !==
                'این پروژه دارای سوابق پرداخت است و امکان حذف فیزیکی آن وجود ندارد.'
            ) {
                return null;
            }

            return ApiResponse::error(
                message: $e->getMessage(),
                status: 409,
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
                    ? 'خطایی در سرور رخ داده است. لطفاً کمی بعد دوباره تلاش کنید.'
                    : $e->getMessage(),

                status: 500,
            );

        });
    })

    ->create();
