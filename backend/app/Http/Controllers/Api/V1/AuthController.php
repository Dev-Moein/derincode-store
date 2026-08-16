<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\VerifyOtpRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {}

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            $request->validated()
        );

        return ApiResponse::success(
            data: [
                'user' => new UserResource(
                    $result['user']->load('roles')
                ),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
            message: 'Registration successful.',
            status: 201,
        );
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
        );

        if (!$result) {
            return ApiResponse::error(
                message: 'Invalid credentials.',
                status: 401,
            );
        }

        return ApiResponse::success(
            data: [
                'user' => new UserResource(
                    $result['user']->load('roles')
                ),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
            message: 'Login successful.',
        );
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            data: [
                'user' => new UserResource(
                    $request->user()->load('roles')
                ),
            ],
            message: 'Authenticated user.',
        );
    }

    /**
     * Logout authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout(
            $request->user()
        );

        return ApiResponse::success(
            message: 'Logged out successfully.',
        );
    }

    /**
     * Send password reset OTP.
     */
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {
        $this->authService->forgotPassword(
            $request->string('email')->toString()
        );

        return ApiResponse::success(
            message: 'If the email exists, an OTP has been sent.',
        );
    }

    /**
     * Verify password reset OTP.
     */
    public function verifyOtp(
        VerifyOtpRequest $request
    ): JsonResponse {
        $isValid = $this->authService->verifyOtp(
            $request->string('email')->toString(),
            $request->string('otp')->toString(),
        );

        if (!$isValid) {
            return ApiResponse::error(
                message: 'Invalid or expired OTP.',
                status: 422,
            );
        }

        return ApiResponse::success(
            message: 'OTP verified successfully.',
        );
    }

    /**
     * Reset user password.
     */
    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {
        $success = $this->authService->resetPassword(
            $request->string('email')->toString(),
            $request->string('otp')->toString(),
            $request->string('password')->toString(),
        );

        if (!$success) {
            return ApiResponse::error(
                message: 'Invalid or expired OTP.',
                status: 422,
            );
        }

        return ApiResponse::success(
            message: 'Password reset successfully.',
        );
    }
}
