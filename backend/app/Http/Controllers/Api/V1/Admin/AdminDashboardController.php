<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\AdminDashboardResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly AdminDashboardServiceInterface $dashboardService,
    ) {}

    public function index(): JsonResponse
    {
        $dashboard =
            $this->dashboardService->getDashboard();

        return ApiResponse::success(
            data: new AdminDashboardResource(
                $dashboard
            ),
            message:
                'Admin dashboard retrieved successfully.',
        );
    }
}
