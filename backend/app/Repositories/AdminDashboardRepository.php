<?php

namespace App\Repositories;

use App\Contracts\Repositories\AdminDashboardRepositoryInterface;
use App\Enums\PaymentStatus;
use App\Models\Download;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Models\User;

class AdminDashboardRepository implements AdminDashboardRepositoryInterface
{
    public function getStats(): array
    {
        return [
            'users' => User::count(),

            'projects' => Project::count(),

            'published_projects' => Project::where(
                'status',
                'published'
            )->count(),

            'sales_count' => Payment::where(
                'status',
                PaymentStatus::SUCCESSFUL
            )->count(),

            'revenue' => Payment::where(
                'status',
                PaymentStatus::SUCCESSFUL
            )->sum('amount'),

            'downloads' => Download::count(),

            'pending_requests' => ProjectRequest::where(
                'status',
                'pending'
            )->count(),
        ];
    }

    public function getRecentUsers(
        int $limit = 5
    ) {
        return User::query()
            ->latest()
            ->limit($limit)
            ->get([
                'id',
                'name',
                'email',
                'created_at',
            ]);
    }

    public function getRecentPayments(
        int $limit = 5
    ) {
        return Payment::query()
            ->with([
                'user:id,name,email',
                'project:id,title,slug',
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getRecentProjectRequests(
        int $limit = 5
    ) {
        return ProjectRequest::query()
            ->with([
                'user:id,name,email',
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
