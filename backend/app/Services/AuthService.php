<?php

namespace App\Services;

use App\Contracts\Notifications\OtpNotificationInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\OtpServiceInterface;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly OtpServiceInterface $otpService,
        private readonly OtpNotificationInterface $otpNotification,
    ) {}

    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create($data);

            /*
             * Make sure the default customer role exists.
             *
             * This is important for tests and fresh installations
             * where the roles table may not have been seeded yet.
             */
            $customerRole = Role::firstOrCreate(
                ['slug' => 'customer'],
                [
                    'name' => 'Customer',
                    'description' => 'Default customer role.',
                ],
            );

            $user->roles()->syncWithoutDetaching([
                $customerRole->id,
            ]);

            $token = $user
                ->createToken('auth-token')
                ->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    /**
     * Login user.
     */
    public function login(
        string $email,
        string $password
    ): ?array {
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user
            ->createToken('auth-token')
            ->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout authenticated user.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    /**
     * Generate and send password reset OTP.
     */
    public function forgotPassword(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user) {
            return;
        }

        $otp = $this->otpService->generate($email);

        $this->otpNotification->send(
            $email,
            $otp
        );
    }

    /**
     * Verify password reset OTP.
     */
    public function verifyOtp(
        string $email,
        string $otp
    ): bool {
        return $this->otpService->verify(
            $email,
            $otp
        );
    }

    /**
     * Reset user password.
     */
    public function resetPassword(
        string $email,
        string $otp,
        string $password
    ): bool {
        return DB::transaction(function () use (
            $email,
            $otp,
            $password
        ) {
            if (! $this->otpService->verify($email, $otp)) {
                return false;
            }

            $user = $this->userRepository->findByEmail($email);

            if (! $user) {
                return false;
            }

            $user->update([
                'password' => $password,
            ]);

            // Invalidate OTP after successful password reset.
            $this->otpService->forget($email);

            // Revoke all existing access tokens.
            $user->tokens()->delete();

            return true;
        });
    }
}
