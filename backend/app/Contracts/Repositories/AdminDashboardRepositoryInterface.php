<?php

namespace App\Contracts\Repositories;

interface AdminDashboardRepositoryInterface
{
    public function getStats(): array;

    public function getRecentUsers(int $limit = 5);

    public function getRecentPayments(int $limit = 5);

    public function getRecentProjectRequests(int $limit = 5);
}
