<?php

namespace App\Services;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class ZarinpalService implements PaymentGatewayInterface
{
    private string $merchantId;

    private string $requestUrl;

    private string $verifyUrl;

    private string $paymentUrl;


    public function __construct()
    {
        $this->merchantId = (string) config(
            'services.zarinpal.merchant_id',
            ''
        );

        $this->requestUrl = (string) config(
            'services.zarinpal.request_url',
            'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
        );

        $this->verifyUrl = (string) config(
            'services.zarinpal.verify_url',
            'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
        );

        $this->paymentUrl = (string) config(
            'services.zarinpal.payment_url',
            'https://sandbox.zarinpal.com/pg/StartPay/'
        );
    }


    public function request(
        Payment $payment,
        string $callbackUrl
    ): array {

        $response = Http::timeout(15)
            ->post(
                $this->requestUrl,
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
            (int) $data['data']['code'] !== 100
        ) {
            return [
                'success' => false,
                'data' => $data,
            ];
        }


        $authority = $data['data']['authority'] ?? null;


        if (! $authority) {
            return [
                'success' => false,
                'data' => $data,
            ];
        }


        return [
            'success' => true,

            'authority' => $authority,

            'payment_url' => $this->paymentUrl . $authority,
        ];
    }



    public function paymentUrl(Payment $payment): ?string
    {
        if (! $payment->authority) {
            return null;
        }

        return $this->paymentUrl . $payment->authority;
    }

    public function verify(
        Payment $payment,
        string $authority
    ): array {


        $response = Http::timeout(15)
            ->post(
                $this->verifyUrl,
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


        $code = (int) $data['data']['code'];


        return [
            'success' => in_array(
                $code,
                [100, 101],
                true
            ),

            'code' => $code,

            'ref_id' => $data['data']['ref_id'] ?? null,

            'data' => $data,
        ];
    }
}
