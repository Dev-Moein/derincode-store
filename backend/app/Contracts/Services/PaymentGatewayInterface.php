<?php

namespace App\Contracts\Services;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function request(
        Payment $payment,
        string $callbackUrl
    ): array;

    public function verify(
        Payment $payment,
        string $authority
    ): array;
}
