<?php

namespace App\Contracts\Services;

interface OtpServiceInterface
{
    public function generate(string $email): string;

    public function verify(string $email, string $otp): bool;

    public function forget(string $email): void;
}
