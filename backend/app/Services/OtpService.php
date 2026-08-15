<?php

namespace App\Services;

use App\Contracts\Services\OtpServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class OtpService implements OtpServiceInterface
{
    private const TTL = 300;

    public function generate(string $email): string
    {
        $otp = (string) random_int(100000, 999999);

        Cache::put(
            $this->key($email),
            Hash::make($otp),
            now()->addSeconds(self::TTL)
        );

        return $otp;
    }

    public function verify(
        string $email,
        string $otp
    ): bool {
        $hashedOtp = Cache::get(
            $this->key($email)
        );

        if (!$hashedOtp) {
            return false;
        }

        return Hash::check(
            $otp,
            $hashedOtp
        );
    }

    public function forget(string $email): void
    {
        Cache::forget(
            $this->key($email)
        );
    }

    private function key(string $email): string
    {
        return 'password-reset-otp:' . strtolower(
            trim($email)
        );
    }
}
