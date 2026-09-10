<?php

namespace App\Providers;

use App\Contracts\Notifications\OtpNotificationInterface;
use App\Contracts\Repositories\AdminDashboardRepositoryInterface;
use App\Contracts\Repositories\ContactRepositoryInterface;
use App\Contracts\Repositories\DownloadRepositoryInterface;
use App\Contracts\Repositories\PaymentRepositoryInterface;
use App\Contracts\Repositories\ProjectImageRepositoryInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\ProjectRequestRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Contracts\Services\AdminUserServiceInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\ContactServiceInterface;
use App\Contracts\Services\DownloadServiceInterface;
use App\Contracts\Services\OtpServiceInterface;
use App\Contracts\Services\PaymentGatewayInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Contracts\Services\ProjectFileServiceInterface;
use App\Contracts\Services\ProjectImageServiceInterface;
use App\Contracts\Services\ProjectRequestServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Notifications\Otp\EmailOtpNotification;
use App\Repositories\AdminDashboardRepository;
use App\Repositories\ContactRepository;
use App\Repositories\DownloadRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\ProjectImageRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectRequestRepository;
use App\Repositories\UserRepository;
use App\Services\AdminDashboardService;
use App\Services\AdminUserService;
use App\Services\AuthService;
use App\Services\ContactService;
use App\Services\DownloadService;
use App\Services\OtpService;
use App\Services\PaymentService;
use App\Services\ProjectFileService;
use App\Services\ProjectImageService;
use App\Services\ProjectRequestService;
use App\Services\ProjectService;
use App\Services\ZarinpalService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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

        $this->app->bind(
            ProjectRequestRepositoryInterface::class,
            ProjectRequestRepository::class
        );
        $this->app->bind(
            ProjectRequestServiceInterface::class,
            ProjectRequestService::class
        );

        $this->app->bind(
            PaymentRepositoryInterface::class,
            PaymentRepository::class
        );

        $this->app->bind(
            PaymentServiceInterface::class,
            PaymentService::class
        );

        $this->app->bind(
            PaymentGatewayInterface::class,
            ZarinpalService::class
        );
        $this->app->bind(
            ProjectFileServiceInterface::class,
            ProjectFileService::class,
        );
        $this->app->bind(
            DownloadServiceInterface::class,
            DownloadService::class,
        );
        $this->app->bind(
            DownloadRepositoryInterface::class,
            DownloadRepository::class
        );

        $this->app->bind(
            DownloadServiceInterface::class,
            DownloadService::class
        );
        $this->app->bind(
         ContactRepositoryInterface::class,
          ContactRepository::class,
           );
         $this->app->bind(
         ContactServiceInterface::class,
          ContactService::class,
          );
          $this->app->bind(
    AdminDashboardRepositoryInterface::class,
    AdminDashboardRepository::class
);

$this->app->bind(
    AdminDashboardServiceInterface::class,
    AdminDashboardService::class
);
$this->app->bind(
    AdminUserServiceInterface::class,
    AdminUserService::class
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
                    ).'|'.$request->ip()
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
                    ).'|'.$request->ip()
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
                    ).'|'.$request->ip()
                );
        });
    }
}
