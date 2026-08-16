<?php

namespace App\Providers;

use App\Contracts\Notifications\OtpNotificationInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\OtpServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Notifications\Otp\EmailOtpNotification;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Services\ProjectService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\ProjectImageRepositoryInterface;
use App\Contracts\Services\ProjectImageServiceInterface;
use App\Repositories\ProjectImageRepository;
use App\Services\ProjectImageService;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     */
    public function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Repositories
        |--------------------------------------------------------------------------
        */

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            ProjectRepositoryInterface::class,
            ProjectRepository::class
        );

        /*
        |--------------------------------------------------------------------------
        | Services
        |--------------------------------------------------------------------------
        */

        $this->app->bind(
            AuthServiceInterface::class,
            AuthService::class
        );

        $this->app->bind(
            OtpServiceInterface::class,
            OtpService::class
        );

        $this->app->bind(
            ProjectServiceInterface::class,
            ProjectService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        $this->app->bind(
            OtpNotificationInterface::class,
            EmailOtpNotification::class
        );

        $this->app->bind(
    ProjectImageRepositoryInterface::class,
    ProjectImageRepository::class
);

$this->app->bind(
    ProjectImageServiceInterface::class,
    ProjectImageService::class
);
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Login Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by(
                    strtolower(
                        $request->input('email', '')
                    ) . '|' . $request->ip()
                );
        });

        /*
        |--------------------------------------------------------------------------
        | Forgot Password Rate Limiter
        |--------------------------------------------------------------------------
        */

        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinute(3)
                ->by(
                    strtolower(
                        $request->input('email', '')
                    ) . '|' . $request->ip()
                );
        });

        /*
        |--------------------------------------------------------------------------
        | Verify OTP Rate Limiter
        |--------------------------------------------------------------------------
        */

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
