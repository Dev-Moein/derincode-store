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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data' => [
                'user' => new UserResource(
                    $result['user']->load('roles')
                ),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
        ], 201);
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
                'data' => null,
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource(
                    $result['user']->load('roles')
                ),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Authenticated user.',
            'data' => [
                'user' => new UserResource(
                    $request->user()->load('roles')
                ),
            ],
        ]);
    }

    /**
     * Logout authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout(
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
            'data' => null,
        ]);
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

        /*
         * عمداً اطلاعاتی درباره وجود یا عدم وجود
         * ایمیل در سیستم برنمی‌گردانیم.
         *
         * این کار جلوی User Enumeration را می‌گیرد.
         */
        return response()->json([
            'success' => true,
            'message' => 'If the email exists, an OTP has been sent.',
            'data' => null,
        ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
                'data' => null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'data' => null,
        ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
                'data' => null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
            'data' => null,
        ]);
    }
}
