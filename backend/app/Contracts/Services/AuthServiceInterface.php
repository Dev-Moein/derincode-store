<?php

namespace App\Contracts\Services;

use App\Models\User;

interface AuthServiceInterface
{
    public function register(array $data): array;

    public function login(
        string $email,
        string $password
    ): ?array;

    public function logout(User $user): void;

    public function forgotPassword(string $email): void;

    public function verifyOtp(
        string $email,
        string $otp
    ): bool;

    public function resetPassword(
        string $email,
        string $otp,
        string $password
    ): bool;
}
