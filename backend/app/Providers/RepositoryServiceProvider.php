<?php

namespace App\Providers;

use App\Contracts\Notifications\OtpNotificationInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\OtpServiceInterface;
use App\Notifications\Otp\EmailOtpNotification;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\OtpService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );
          $this->app->bind(
        OtpServiceInterface::class,
        OtpService::class
    );

    $this->app->bind(
        OtpNotificationInterface::class,
        EmailOtpNotification::class
    );
    }

    public function boot(): void
    {
           RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(5)
            ->by(
                strtolower(
                    $request->input('email', '')
                ) . '|' . $request->ip()
            );
    });

    RateLimiter::for('forgot-password', function (Request $request) {
        return Limit::perMinute(3)
            ->by(
                strtolower(
                    $request->input('email', '')
                ) . '|' . $request->ip()
            );
    });

    RateLimiter::for('verify-otp', function (Request $request) {
        return Limit::perMinutes(5, 5)
            ->by(
                strtolower(
                    $request->input('email', '')
                ) . '|' . $request->ip()
            );
    });
    }
}
