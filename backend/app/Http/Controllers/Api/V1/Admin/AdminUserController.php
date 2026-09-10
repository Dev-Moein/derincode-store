<?php


namespace App\Http\Controllers\Api\V1\Admin;

use App\Contracts\Services\AdminUserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Admin\AdminUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserServiceInterface $adminUserService,
    ) {}


    /**
     * Admin users list
     */
    public function index(Request $request)
    {
        $users = $this->adminUserService->paginate(
            $request->only([
                'search',
                'per_page',
            ])
        );

        return AdminUserResource::collection($users);
    }


    /**
     * Show user
     */
    public function show(
        int $id
    ): JsonResponse {

        $user = $this->adminUserService->find(
            $id
        );

        if (! $user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
            ], 404);
        }


        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => new AdminUserResource($user),
        ]);
    }


    /**
     * Update user role
     */
    public function updateRole(
        Request $request,
        int $id
    ): JsonResponse {

        $request->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,slug',
            ],
        ]);


        $user = $this->adminUserService->find(
            $id
        );


        if (! $user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
            ], 404);
        }


        $user = $this->adminUserService->updateRole(
            $user,
            $request->input('role')
        );


        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully.',
            'data' => new AdminUserResource($user),
        ]);
    }


    /**
     * Delete user
     */
    public function destroy(
        int $id
    ): JsonResponse {

        $user = $this->adminUserService->find(
            $id
        );


        if (! $user) {

            return response()->json([
                'success' => false,
                'message' => 'User not found.',
                'data' => null,
            ], 404);
        }


        $this->adminUserService->delete(
            $user
        );


        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
            'data' => null,
        ]);
    }
}
