<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\ProjectRequestServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreProjectRequestRequest;
use App\Http\Requests\Api\V1\UpdateProjectRequestStatusRequest;
use App\Http\Resources\Api\V1\ProjectRequestResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectRequestController extends Controller
{
    public function __construct(
        private readonly ProjectRequestServiceInterface $projectRequestService,
    ) {}

    /**
     * User's own project requests.
     */
    public function index(Request $request)
    {
        $requests = $this->projectRequestService->paginateForUser(
            auth()->id(),
            $request->only([
                'status',
                'per_page',
            ])
        );

        return ProjectRequestResource::collection($requests);
    }

    /**
     * Create a new project request.
     */
    public function store(
        StoreProjectRequestRequest $request
    ): JsonResponse {
        $projectRequest = $this->projectRequestService->create([
            ...$request->validated(),
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        $projectRequest->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Project request submitted successfully.',
            'data' => new ProjectRequestResource($projectRequest),
        ], 201);
    }

    /**
     * Admin: list all project requests.
     */
    public function adminIndex(Request $request)
    {
        $requests = $this->projectRequestService->paginateForAdmin(
            $request->only([
                'search',
                'status',
                'per_page',
            ])
        );

        return ProjectRequestResource::collection($requests);
    }

    /**
     * Admin: view a single request.
     */
    public function show(int $id): JsonResponse
    {
        $projectRequest = $this->projectRequestService->findById($id);

        if (! $projectRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Project request not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Project request retrieved successfully.',
            'data' => new ProjectRequestResource($projectRequest),
        ]);
    }

    /**
     * Admin: update project request status.
     */
    public function updateStatus(
        UpdateProjectRequestStatusRequest $request,
        int $id
    ): JsonResponse {
        $projectRequest = $this->projectRequestService->findById($id);

        if (! $projectRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Project request not found.',
                'data' => null,
            ], 404);
        }

        $projectRequest = $this->projectRequestService->updateStatus(
            $projectRequest,
            $request->validated('status'),
            $request->validated('admin_note')
        );

        return response()->json([
            'success' => true,
            'message' => 'Project request status updated successfully.',
            'data' => new ProjectRequestResource($projectRequest),
        ]);
    }
}
