<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Payment;
use App\Models\ProjectRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get authenticated user's dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $projectRequests = $user
            ->projectRequests()
            ->latest()
            ->get();

        $payments = $user
            ->payments()
            ->with('project')
            ->where('status', PaymentStatus::SUCCESSFUL)
            ->latest('paid_at')
            ->get();

        $downloads = $user
            ->downloads()
            ->with('project')
            ->latest('downloaded_at')
            ->get();

        return ApiResponse::success(
            data: [
                'user' => new UserResource(
                    $user->load('roles')
                ),

                'stats' => [
                    'project_requests' => $projectRequests->count(),

                    'purchased_projects' => $payments
                        ->pluck('project_id')
                        ->unique()
                        ->count(),

                    'downloads' => $downloads->count(),
                ],

                'project_requests' => $projectRequests,

                'purchased_projects' => $payments,

                'recent_downloads' => $downloads,
            ],
            message: 'Dashboard retrieved successfully.',
        );
    }
}

