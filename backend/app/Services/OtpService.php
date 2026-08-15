<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class OtpService
{
    private const TTL = 300;

    public function generate(string $email): string
    {
        $otp = (string) random_int(100000, 999999);

        Cache::put(
            $this->key($email),
            $otp,
            now()->addSeconds(self::TTL)
        );

        return $otp;
    }

    public function verify(string $email, string $otp): bool
    {
        return Cache::get($this->key($email)) === $otp;
    }

    public function forget(string $email): void
    {
        Cache::forget($this->key($email));
    }

    private function key(string $email): string
    {
        return 'password-reset-otp:' . strtolower($email);
    }
}
