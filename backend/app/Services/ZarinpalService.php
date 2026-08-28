<?php

namespace App\Services;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class ZarinpalService implements PaymentGatewayInterface
{
    private string $merchantId;

    public function __construct()
    {
        $this->merchantId = config(
            'services.zarinpal.merchant_id'
        );
    }

    public function request(
        Payment $payment,
        string $callbackUrl
    ): array {
        $response = Http::post(
            'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            [
                'merchant_id' => $this->merchantId,
                'amount' => (int) $payment->amount,
                'callback_url' => $callbackUrl,
                'description' => sprintf(
                    'Purchase project #%d',
                    $payment->project_id
                ),
            ]
        );

        $data = $response->json();

        if (
            ! $response->successful() ||
            ! isset($data['data']['code']) ||
            $data['data']['code'] !== 100
        ) {
            return [
                'success' => false,
                'data' => $data,
            ];
        }

        $authority = $data['data']['authority'];

        return [
            'success' => true,
            'authority' => $authority,
            'payment_url' => 'https://sandbox.zarinpal.com/pg/StartPay/'
                .$authority,
        ];
    }

    public function verify(
        Payment $payment,
        string $authority
    ): array {
        $response = Http::post(
            'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            [
                'merchant_id' => $this->merchantId,
                'amount' => (int) $payment->amount,
                'authority' => $authority,
            ]
        );

        $data = $response->json();

        if (
            ! $response->successful() ||
            ! isset($data['data']['code'])
        ) {
            return [
                'success' => false,
                'data' => $data,
            ];
        }

        return [
            'success' => in_array(
                $data['data']['code'],
                [100, 101],
                true
            ),
            'code' => $data['data']['code'],
            'ref_id' => $data['data']['ref_id'] ?? null,
            'data' => $data,
        ];
    }
}
