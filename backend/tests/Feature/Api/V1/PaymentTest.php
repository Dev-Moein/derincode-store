<?php

namespace Tests\Feature\Api\V1;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        return $user;
    }

    private function purchasableProject(): Project
    {
        return Project::factory()->create([
            'status' => ProjectStatus::PUBLISHED,
            'is_for_sale' => true,
            'price' => 1500,
            'currency' => 'USD',
        ]);
    }

    /**
     * Replace the real payment gateway with a mock.
     */
    private function mockPaymentGateway(
        array $requestResult = [],
        array $verifyResult = []
    ): void {
        $gateway = Mockery::mock(
            PaymentGatewayInterface::class
        );

        if ($requestResult !== []) {
            $gateway
                ->shouldReceive('request')
                ->andReturn($requestResult);
        }

        if ($verifyResult !== []) {
            $gateway
                ->shouldReceive('verify')
                ->andReturn($verifyResult);
        }

        $this->app->instance(
            PaymentGatewayInterface::class,
            $gateway
        );
    }

    public function test_authenticated_user_can_list_own_payments(): void
    {
        $user = $this->user();

        Payment::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        Payment::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/payments');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_unauthenticated_user_cannot_list_payments(): void
    {
        $response = $this->getJson(
            '/api/v1/payments'
        );

        $response->assertUnauthorized();
    }

    public function test_user_only_sees_own_payments(): void
    {
        $user = $this->user();

        Payment::factory()->create([
            'user_id' => $user->id,
            'transaction_id' => 'MY-PAYMENT',
        ]);

        Payment::factory()->create([
            'transaction_id' => 'OTHER-PAYMENT',
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/payments');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'transaction_id' => 'MY-PAYMENT',
            ])
            ->assertJsonMissing([
                'transaction_id' => 'OTHER-PAYMENT',
            ]);
    }

    public function test_payments_can_be_filtered_by_status(): void
    {
        $user = $this->user();

        Payment::factory()
            ->successful()
            ->create([
                'user_id' => $user->id,
                'transaction_id' => 'SUCCESS-PAYMENT',
            ]);

        Payment::factory()
            ->pending()
            ->create([
                'user_id' => $user->id,
                'transaction_id' => 'PENDING-PAYMENT',
            ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            '/api/v1/payments?status=successful'
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'transaction_id' => 'SUCCESS-PAYMENT',
                'status' => 'successful',
            ])
            ->assertJsonMissing([
                'transaction_id' => 'PENDING-PAYMENT',
            ]);
    }

    public function test_authenticated_user_can_initiate_payment(): void
    {
        $user = $this->user();

        $project = $this->purchasableProject();

        $this->mockPaymentGateway(
            requestResult: [
                'success' => true,
                'authority' => 'TEST-AUTHORITY-123',
                'payment_url' => 'https://example.com/payment/TEST-AUTHORITY-123',
            ]
        );

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/payments', [
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Payment initiated successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'payment',
                    'payment_url',
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => $project->price,
            'currency' => $project->currency,
            'gateway' => 'zarinpal',
            'status' => PaymentStatus::PENDING->value,
        ]);
    }

    public function test_payment_validation_works(): void
    {
        $user = $this->user();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/payments', [
            'project_id' => '',
            'gateway' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ]);
    }

    public function test_user_cannot_purchase_non_sale_project(): void
    {
        $user = $this->user();

        $project = Project::factory()->create([
            'status' => ProjectStatus::PUBLISHED,
            'is_for_sale' => false,
            'price' => 1500,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/payments', [
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
        ]);

        $response
            ->assertStatus(422);
    }

    public function test_user_cannot_purchase_unpublished_project(): void
    {
        $user = $this->user();

        $project = Project::factory()->create([
            'status' => ProjectStatus::DRAFT,
            'is_for_sale' => true,
            'price' => 1500,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/payments', [
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
        ]);

        $response
            ->assertStatus(422);
    }

    public function test_user_cannot_purchase_project_with_invalid_price(): void
    {
        $user = $this->user();

        $project = Project::factory()->create([
            'status' => ProjectStatus::PUBLISHED,
            'is_for_sale' => true,
            'price' => 0,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/payments', [
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
        ]);

        $response
            ->assertStatus(422);
    }

    public function test_user_cannot_purchase_same_project_twice_after_successful_payment(): void
    {
        $user = $this->user();

        $project = $this->purchasableProject();

        Payment::factory()
            ->successful()
            ->create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/payments', [
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
        ]);

        $response
            ->assertStatus(422);
    }

    public function test_user_can_view_own_payment(): void
    {
        $user = $this->user();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/payments/{$payment->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Payment retrieved successfully.',
            ])
            ->assertJsonPath(
                'data.id',
                $payment->id
            );
    }

    public function test_user_cannot_view_another_users_payment(): void
    {
        $user = $this->user();

        $payment = Payment::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/payments/{$payment->id}"
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Payment not found.',
            ]);
    }

    public function test_unknown_payment_returns_404(): void
    {
        $user = $this->user();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/payments/999999');

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Payment not found.',
            ]);
    }

    public function test_callback_returns_404_for_unknown_payment(): void
    {
        $response = $this->getJson(
            '/api/v1/payments/999999/callback?Authority=TEST&Status=OK'
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Payment not found.',
            ]);
    }

    public function test_callback_requires_authority(): void
    {
        $payment = Payment::factory()
            ->pending()
            ->create();

        $response = $this->getJson(
            "/api/v1/payments/{$payment->id}/callback?Status=OK"
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Payment authority is missing.',
            ]);
    }

    public function test_cancelled_payment_callback_marks_payment_as_cancelled(): void
    {
        $payment = Payment::factory()
            ->pending()
            ->create();

        $response = $this->getJson(
            "/api/v1/payments/{$payment->id}/callback?".
            'Authority=TEST-AUTHORITY&Status=NOK'
        );

        $response
            ->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Payment was cancelled.',
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::CANCELLED->value,
        ]);
    }

    public function test_failed_payment_verification_marks_payment_as_failed(): void
    {
        $payment = Payment::factory()
            ->pending()
            ->create();

        $this->mockPaymentGateway(
            verifyResult: [
                'success' => false,
                'code' => -22,
            ]
        );

        $response = $this->getJson(
            "/api/v1/payments/{$payment->id}/callback?".
            'Authority=TEST-AUTHORITY&Status=OK'
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Payment verification failed.',
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::FAILED->value,
        ]);
    }

    public function test_successful_payment_callback_marks_payment_as_successful(): void
    {
        $payment = Payment::factory()
            ->pending()
            ->create();

        $this->mockPaymentGateway(
            verifyResult: [
                'success' => true,
                'ref_id' => 'REF-123456',
            ]
        );

        $response = $this->getJson(
            "/api/v1/payments/{$payment->id}/callback?".
            'Authority=TEST-AUTHORITY&Status=OK'
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Payment verified successfully.',
                'data' => [
                    'ref_id' => 'REF-123456',
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => PaymentStatus::SUCCESSFUL->value,
            'transaction_id' => 'REF-123456',
        ]);
    }

    public function test_successful_payment_callback_is_idempotent(): void
    {
        $payment = Payment::factory()
            ->successful()
            ->create();

        $response = $this->getJson(
            "/api/v1/payments/{$payment->id}/callback"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Payment has already been verified.',
            ]);
    }

    public function test_unauthenticated_user_cannot_view_single_payment(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->getJson(
            "/api/v1/payments/{$payment->id}"
        );

        $response->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_initiate_payment(): void
    {
        $project = $this->purchasableProject();

        $response = $this->postJson('/api/v1/payments', [
            'project_id' => $project->id,
            'gateway' => 'zarinpal',
        ]);

        $response->assertUnauthorized();
    }
}
