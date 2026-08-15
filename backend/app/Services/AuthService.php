<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Jobs\SendPasswordResetOtpJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly OtpService $otpService,
    ) {}

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create($data);

            $customerRole = Role::where('slug', 'customer')
                ->firstOrFail();

            $user->roles()->attach($customerRole);

            $token = $user
                ->createToken('auth-token')
                ->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
            ];
        });
    }

    public function login(
        string $email,
        string $password
    ): ?array {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
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

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function forgotPassword(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return;
        }

        $otp = $this->otpService->generate($email);

        SendPasswordResetOtpJob::dispatch(
            $email,
            $otp
        );
    }

    public function verifyOtp(
        string $email,
        string $otp
    ): bool {
        return $this->otpService->verify(
            $email,
            $otp
        );
    }

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
            if (!$this->otpService->verify($email, $otp)) {
                return false;
            }

            $user = $this->userRepository->findByEmail($email);

            if (!$user) {
                return false;
            }

            $user->update([
                'password' => $password,
            ]);

            // OTP بعد از استفاده باطل می‌شود.
            $this->otpService->forget($email);

            // تمام Tokenهای قبلی کاربر باطل می‌شوند.
            $user->tokens()->delete();

            return true;
        });
    }
}
