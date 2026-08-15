<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Repositories\UserRepository;
use App\Services\AuthService;
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
    }

    public function boot(): void
    {
        //
    }
}
