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
        //
    }
}
