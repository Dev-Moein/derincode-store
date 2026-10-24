<?php

namespace Tests\Unit\Services;

use App\Models\Payment;
use App\Services\ZarinpalService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ZarinpalServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.zarinpal.merchant_id' => 'test-merchant',
            'services.zarinpal.request_url' => 'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            'services.zarinpal.verify_url' => 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            'services.zarinpal.payment_url' => 'https://sandbox.zarinpal.com/pg/StartPay/',
        ]);
    }

    public function test_request_converts_toman_to_rial_and_uses_sandbox(): void
    {
        Http::fake([
            'sandbox.zarinpal.com/pg/v4/payment/request.json' => Http::response([
                'data' => [
                    'code' => 100,
                    'authority' => 'A000000000000000000000000000000001',
                ],
            ], 200),
        ]);

        $payment = new Payment([
            'project_id' => 12,
            'amount' => 150000,
            'currency' => 'IRT',
        ]);

        $result = app(ZarinpalService::class)->request(
            $payment,
            'https://example.test/api/payments/callback/1'
        );

        $this->assertTrue($result['success']);
        $this->assertSame(
            'https://sandbox.zarinpal.com/pg/StartPay/A000000000000000000000000000000001',
            $result['payment_url']
        );

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
                && $request['amount'] === 1500000
                && $request['merchant_id'] === 'test-merchant';
        });
    }

    public function test_request_rejects_usd_instead_of_silently_treating_it_as_toman(): void
    {
        Http::fake();

        $payment = new Payment([
            'project_id' => 12,
            'amount' => 1500,
            'currency' => 'USD',
        ]);

        $result = app(ZarinpalService::class)->request(
            $payment,
            'https://example.test/api/payments/callback/1'
        );

        $this->assertFalse($result['success']);
        Http::assertNothingSent();
    }

    public function test_verify_requires_matching_authority_and_transaction_reference(): void
    {
        Http::fake([
            'sandbox.zarinpal.com/pg/v4/payment/verify.json' => Http::response([
                'data' => [
                    'code' => 100,
                    'ref_id' => 987654321,
                ],
            ], 200),
        ]);

        $payment = new Payment([
            'project_id' => 12,
            'amount' => 150000,
            'currency' => 'IRR',
            'authority' => 'AUTH-123',
        ]);

        $result = app(ZarinpalService::class)->verify($payment, 'AUTH-123');

        $this->assertTrue($result['success']);
        $this->assertSame('987654321', $result['ref_id']);

        $invalid = app(ZarinpalService::class)->verify($payment, 'AUTH-WRONG');
        $this->assertFalse($invalid['success']);
    }
}
