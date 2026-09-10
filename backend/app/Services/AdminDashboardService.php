<?php

namespace App\Services;

use App\Contracts\Repositories\AdminDashboardRepositoryInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;

class AdminDashboardService
    implements AdminDashboardServiceInterface
{
    public function __construct(
        private readonly AdminDashboardRepositoryInterface $repository,
    ) {}

    public function getDashboard(): array
    {
        return [
            'stats' => $this->repository->getStats(),

            'recent_users' =>
                $this->repository->getRecentUsers(),

            'recent_payments' =>
                $this->repository->getRecentPayments(),

            'recent_project_requests' =>
                $this->repository->getRecentProjectRequests(),
        ];
    }
}
