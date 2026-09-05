<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\PaymentRepositoryInterface;
use App\Contracts\Services\PaymentGatewayInterface;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentRepositoryInterface $paymentRepository;

    private PaymentGatewayInterface $paymentGateway;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentRepository = Mockery::mock(
            PaymentRepositoryInterface::class
        );

        $this->paymentGateway = Mockery::mock(
            PaymentGatewayInterface::class
        );

        $this->paymentService = new PaymentService(
            $this->paymentRepository,
            $this->paymentGateway,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }


private function createPendingPayment(): Payment
{
    $user = User::factory()->create();

    $project = Project::factory()->create();

    return Payment::create([
        'user_id' => $user->id,
        'project_id' => $project->id,
        'amount' => 100,
        'currency' => 'IRR',
        'gateway' => 'zarinpal',
        'status' => PaymentStatus::PENDING,
    ]);
}


    public function test_it_marks_payment_as_successful(): void
    {
        $payment = $this->createPendingPayment();

        $updatedPayment = new Payment([
            'status' => PaymentStatus::SUCCESSFUL,
            'transaction_id' => 'REF-123456',
        ]);

        $updatedPayment->id = $payment->id;

        $this->paymentRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(function (Payment $model) use ($payment) {
                    return $model->id === $payment->id
                        && $model->status === PaymentStatus::PENDING;
                }),
                Mockery::on(function (array $data) {
                    return $data['status']
                        === PaymentStatus::SUCCESSFUL
                        && $data['transaction_id']
                        === 'REF-123456'
                        && isset($data['paid_at']);
                })
            )
            ->andReturn($updatedPayment);

        $result = $this->paymentService->markAsSuccessful(
            $payment,
            'REF-123456'
        );

        $this->assertSame(
            PaymentStatus::SUCCESSFUL,
            $result->status
        );

        $this->assertSame(
            'REF-123456',
            $result->transaction_id
        );
    }

    public function test_it_marks_payment_as_failed(): void
    {
        $payment = $this->createPendingPayment();

        $updatedPayment = new Payment([
            'status' => PaymentStatus::FAILED,
        ]);

        $updatedPayment->id = $payment->id;

        $this->paymentRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(function (Payment $model) use ($payment) {
                    return $model->id === $payment->id
                        && $model->status === PaymentStatus::PENDING;
                }),
                [
                    'status' => PaymentStatus::FAILED,
                ]
            )
            ->andReturn($updatedPayment);

        $result = $this->paymentService->markAsFailed(
            $payment
        );

        $this->assertSame(
            PaymentStatus::FAILED,
            $result->status
        );
    }

    public function test_it_marks_payment_as_cancelled(): void
    {
        $payment = $this->createPendingPayment();

        $updatedPayment = new Payment([
            'status' => PaymentStatus::CANCELLED,
        ]);

        $updatedPayment->id = $payment->id;

        $this->paymentRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(function (Payment $model) use ($payment) {
                    return $model->id === $payment->id
                        && $model->status === PaymentStatus::PENDING;
                }),
                [
                    'status' => PaymentStatus::CANCELLED,
                ]
            )
            ->andReturn($updatedPayment);

        $result = $this->paymentService->markAsCancelled(
            $payment
        );

        $this->assertSame(
            PaymentStatus::CANCELLED,
            $result->status
        );
    }

    public function test_it_can_check_if_user_has_purchased_project(): void
    {
        $payment = new Payment([
            'user_id' => 1,
            'project_id' => 10,
            'status' => PaymentStatus::SUCCESSFUL,
        ]);

        $this->paymentRepository
            ->shouldReceive('findSuccessfulPayment')
            ->once()
            ->with(1, 10)
            ->andReturn($payment);

        $result = $this->paymentService->hasPurchasedProject(
            1,
            10
        );

        $this->assertTrue($result);
    }

    public function test_it_returns_false_when_user_has_not_purchased_project(): void
    {
        $this->paymentRepository
            ->shouldReceive('findSuccessfulPayment')
            ->once()
            ->with(1, 10)
            ->andReturnNull();

        $result = $this->paymentService->hasPurchasedProject(
            1,
            10
        );

        $this->assertFalse($result);
    }

    public function test_it_initiates_pending_payment_successfully(): void
    {
        $payment = new Payment([
            'status' => PaymentStatus::PENDING,
        ]);

        $payment->id = 1;

        $updatedPayment = new Payment([
            'status' => PaymentStatus::PENDING,
        ]);

        $updatedPayment->id = 1;

        $callbackUrl = 'https://example.com/callback';

        $this->paymentGateway
            ->shouldReceive('request')
            ->once()
            ->with(
                $payment,
                $callbackUrl
            )
            ->andReturn([
                'success' => true,
                'authority' => 'AUTHORITY-123',
                'payment_url' => 'https://gateway.test/payment',
            ]);

        $this->paymentRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                $payment,
                [
                    'authority' => 'AUTHORITY-123',
                ]
            )
            ->andReturn($updatedPayment);

        $result = $this->paymentService->initiatePayment(
            $payment,
            $callbackUrl
        );

        $this->assertTrue($result['success']);

        $this->assertSame(
            $updatedPayment,
            $result['payment']
        );

        $this->assertSame(
            'AUTHORITY-123',
            $result['authority']
        );

        $this->assertSame(
            'https://gateway.test/payment',
            $result['payment_url']
        );
    }

    public function test_it_marks_payment_as_failed_when_gateway_request_fails(): void
    {
        $payment = $this->createPendingPayment();

        $callbackUrl = 'https://example.com/callback';

        $this->paymentGateway
            ->shouldReceive('request')
            ->once()
            ->with(
                $payment,
                $callbackUrl
            )
            ->andReturn([
                'success' => false,
                'message' => 'Gateway error.',
            ]);

        $updatedPayment = new Payment([
            'status' => PaymentStatus::FAILED,
        ]);

        $updatedPayment->id = $payment->id;

        $this->paymentRepository
            ->shouldReceive('update')
            ->once()
            ->with(
                Mockery::on(function (Payment $model) use ($payment) {
                    return $model->id === $payment->id
                        && $model->status === PaymentStatus::PENDING;
                }),
                [
                    'status' => PaymentStatus::FAILED,
                ]
            )
            ->andReturn($updatedPayment);

        $result = $this->paymentService->initiatePayment(
            $payment,
            $callbackUrl
        );

        $this->assertFalse(
            $result['success']
        );

        $this->assertSame(
            'Gateway error.',
            $result['message']
        );
    }

    public function test_it_cannot_initiate_non_pending_payment(): void
    {
        $payment = new Payment([
            'status' => PaymentStatus::SUCCESSFUL,
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->paymentService->initiatePayment(
            $payment,
            'https://example.com/callback'
        );
    }

    public function test_it_can_find_payment_by_id(): void
    {
        $payment = new Payment([
            'status' => PaymentStatus::PENDING,
        ]);

        $payment->id = 5;

        $this->paymentRepository
            ->shouldReceive('findById')
            ->once()
            ->with(5)
            ->andReturn($payment);

        $result = $this->paymentService->findById(5);

        $this->assertSame(
            $payment,
            $result
        );
    }

    public function test_it_returns_null_when_payment_is_not_found(): void
    {
        $this->paymentRepository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturnNull();

        $result = $this->paymentService->findById(999);

        $this->assertNull($result);
    }
}

