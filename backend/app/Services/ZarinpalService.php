<?php

namespace App\Services;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Models\Payment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ZarinpalService implements PaymentGatewayInterface
{
    private string $merchantId;
    private string $requestUrl;
    private string $verifyUrl;
    private string $paymentUrl;

    public function __construct()
    {
        $this->merchantId = trim((string) config('services.zarinpal.merchant_id', ''));
        $this->requestUrl = (string) config('services.zarinpal.request_url');
        $this->verifyUrl = (string) config('services.zarinpal.verify_url');
        $this->paymentUrl = (string) config('services.zarinpal.payment_url');
    }

    public function request(Payment $payment, string $callbackUrl): array
    {
        if (! $this->isConfigured() || ! $this->hasSupportedAmount($payment)) {
            return ['success' => false, 'reason' => 'invalid_gateway_configuration_or_amount'];
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->post($this->requestUrl, [
                    'merchant_id' => $this->merchantId,
                    'amount' => $this->gatewayAmount($payment),
                    'callback_url' => $callbackUrl,
                    'description' => sprintf('Purchase project #%d', $payment->project_id),
                ]);
        } catch (ConnectionException) {
            return ['success' => false, 'reason' => 'gateway_unavailable'];
        }

        $data = $response->json();
        $authority = data_get($data, 'data.authority');

        if (
            ! $response->successful()
            || (int) data_get($data, 'data.code', 0) !== 100
            || ! is_string($authority)
            || $authority === ''
        ) {
            return ['success' => false, 'reason' => 'gateway_request_rejected'];
        }

        return [
            'success' => true,
            'authority' => $authority,
            'payment_url' => $this->paymentUrl . $authority,
        ];
    }

    public function paymentUrl(Payment $payment): ?string
    {
        if (! $payment->authority || ! $this->isConfigured()) {
            return null;
        }

        return $this->paymentUrl . $payment->authority;
    }

    public function verify(Payment $payment, string $authority): array
    {
        if (
            ! $this->isConfigured()
            || ! $this->hasSupportedAmount($payment)
            || ! $payment->authority
            || ! hash_equals((string) $payment->authority, $authority)
        ) {
            return ['success' => false, 'reason' => 'invalid_gateway_configuration_or_payment'];
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->post($this->verifyUrl, [
                    'merchant_id' => $this->merchantId,
                    'amount' => $this->gatewayAmount($payment),
                    'authority' => $authority,
                ]);
        } catch (ConnectionException) {
            return ['success' => false, 'reason' => 'gateway_unavailable'];
        }

        $data = $response->json();
        $code = (int) data_get($data, 'data.code', 0);

        if (! $response->successful() || ! in_array($code, [100, 101], true)) {
            return ['success' => false, 'code' => $code, 'reason' => 'verification_rejected'];
        }

        $refId = data_get($data, 'data.ref_id');

        if ($refId === null || (string) $refId === '') {
            return ['success' => false, 'code' => $code, 'reason' => 'missing_transaction_reference'];
        }

        return ['success' => true, 'code' => $code, 'ref_id' => (string) $refId];
    }

    private function isConfigured(): bool
    {
        return $this->merchantId !== ''
            && filter_var($this->requestUrl, FILTER_VALIDATE_URL) !== false
            && filter_var($this->verifyUrl, FILTER_VALIDATE_URL) !== false
            && filter_var($this->paymentUrl, FILTER_VALIDATE_URL) !== false
            && str_starts_with($this->requestUrl, 'https://')
            && str_starts_with($this->verifyUrl, 'https://')
            && str_starts_with($this->paymentUrl, 'https://');
    }

    private function hasSupportedAmount(Payment $payment): bool
    {
        // Zarinpal v4 expects integer Rial amounts; do not convert USD/Toman silently.
        if (strtoupper((string) $payment->currency) !== 'IRT') {
            return false;
        }

        $amount = (string) $payment->amount;

        if (preg_match('/^([0-9]+)(?:\.0+)?$/', $amount, $matches) !== 1) {
            return false;
        }

        return (int) $matches[1] > 0 && (int) $matches[1] <= intdiv(PHP_INT_MAX, 10);
    }

    private function gatewayAmount(Payment $payment): int
    {
        // Prices are stored in toman; Zarinpal's API expects Rial.
        preg_match('/^([0-9]+)(?:\.0+)?$/', (string) $payment->amount, $matches);

        return ((int) $matches[1]) * 10;
    }
}
