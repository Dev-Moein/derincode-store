<?php

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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
         * 422 - Validation
         */
        $exceptions->render(function (
            ValidationException $e,
            $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        });

        /*
         * 401 - Unauthenticated
         */
        $exceptions->render(function (
            AuthenticationException $e,
            $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
            ], 401);
        });

        /*
         * 404 - Resource Not Found
         */
        $exceptions->render(function (
            NotFoundHttpException $e,
            $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Resource not found.',
                'data' => null,
            ], 404);
        });

        /*
         * 429 - Too Many Requests
         */
        $exceptions->render(function (
            ThrottleRequestsException $e,
            $request
        ) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
                'data' => null,
            ], 429);
        });
    })
    ->create();
