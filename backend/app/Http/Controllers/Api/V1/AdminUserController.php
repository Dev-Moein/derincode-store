<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AdminUpdateUserRequest;
use App\Http\Requests\Api\V1\AdminUserIndexRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * List users.
     */
    public function index(
        AdminUserIndexRequest $request
    ): JsonResponse {
        $users = $this->userRepository->paginate(
            perPage: $request->integer('per_page', 15),
            search: $request->string('search')->toString() ?: null,
        );

        return ApiResponse::success(
            data: [
                'users' => UserResource::collection($users),
            ],
            message: 'Users retrieved successfully.',
        );
    }

    /**
     * Show a single user.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return ApiResponse::error(
                message: 'User not found.',
                status: 404,
            );
        }

        return ApiResponse::success(
            data: [
                'user' => new UserResource($user),
            ],
            message: 'User retrieved successfully.',
        );
    }

    /**
     * Update a user.
     */
    public function update(
        AdminUpdateUserRequest $request,
        int $id
    ): JsonResponse {
        $user = $this->userRepository->findById($id);

        if (! $user) {
            return ApiResponse::error(
                message: 'User not found.',
                status: 404,
            );
        }

        $user = $this->userRepository->update(
            $user,
            $request->validated()
        );

        return ApiResponse::success(
            data: [
                'user' => new UserResource($user),
            ],
            message: 'User updated successfully.',
        );
    }
}
