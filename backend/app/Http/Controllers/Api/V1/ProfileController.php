<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\DownloadServiceInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ChangePasswordRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Resources\Api\V1\PurchasedProjectResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

  public function __construct(
        private readonly PaymentServiceInterface $paymentService,
    ) {}
    /**
     * Get authenticated user's profile.
     */
     public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $purchasedProjects = $this->paymentService
            ->paginateSuccessfulForUser(
                userId: $user->id,
                perPage: 15,
            );

        return ApiResponse::success(
            data: [
                'user' => new UserResource(
                    $user->load('roles')
                ),

              'purchased_projects' =>
    PurchasedProjectResource::collection(
        $purchasedProjects
    )
            ],
            message: 'Profile retrieved successfully.',
        );
    }

    /**
     * Update authenticated user's profile.
     */
    public function update(
        UpdateProfileRequest $request
    ): JsonResponse {
        $user = $request->user();

        $user->update(
            $request->validated()
        );

        return ApiResponse::success(
            data: [
                'user' => new UserResource(
                    $user->fresh()->load('roles')
                ),
            ],
            message: 'Profile updated successfully.',
        );
    }

    /**
     * Change authenticated user's password.
     */
    public function changePassword(
        ChangePasswordRequest $request
    ): JsonResponse {
        $user = $request->user();

        if (! Hash::check(
            $request->string('current_password')->toString(),
            $user->password
        )) {
            return ApiResponse::error(
                message: 'Current password is incorrect.',
                status: 422,
                errors: [
                    'current_password' => [
                        'The current password is incorrect.',
                    ],
                ],
            );
        }

        $user->update([
            'password' => $request
                ->string('password')
                ->toString(),
        ]);

        /*
         * Revoke all existing tokens.
         *
         * Since the password has changed, the user should authenticate again.
         */
        $user->tokens()->delete();

        return ApiResponse::success(
            message: 'Password changed successfully.',
        );
    }

}

