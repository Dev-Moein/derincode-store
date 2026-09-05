<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Http\Resources\Api\V1\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService,
        private readonly PaymentGatewayInterface $paymentGateway,
    ) {}

    public function index(Request $request)
    {
        $payments = $this->paymentService->paginateForUser(
            $request->user()->id,
            $request->only([
                'status',
                'per_page',
            ])
        );

        return PaymentResource::collection($payments);
    }

    public function show(
        Request $request,
        int $id
    ): JsonResponse {
        $payment = $this->paymentService
            ->findByIdForUser(
                id: $id,
                userId: $request->user()->id,
            );

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment retrieved successfully.',
            'data' => [
                'payment' => new PaymentResource(
                    $payment
                ),
            ],
        ]);
    }

    public function store(
        StorePaymentRequest $request
    ): JsonResponse {
        $payment = $this->paymentService->createForProject(
            user: $request->user(),
            projectId: $request->integer('project_id'),
            gateway: $request->string('gateway')->toString(),
        );

        $result = $this->paymentService->initiatePayment(
            payment: $payment,
            callbackUrl: route(
                'payments.callback',
                [
                    'payment' => $payment->id,
                ]
            ),
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to initiate payment.',
                'data' => null,
            ], 422);
        }

        $payment = $result['payment'];

        $payment->load('project');

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated successfully.',
            'data' => [
                'payment' => new PaymentResource($payment),
                'payment_url' => $result['payment_url'],
            ],
        ], 201);
    }

    public function callback(
        Request $request,
        int $payment
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | 1. Find Payment
        |--------------------------------------------------------------------------
        */

        $paymentModel = $this->paymentService->findById(
            $payment
        );

        if (! $paymentModel) {
            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=error&reason=not-found'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Idempotency
        |--------------------------------------------------------------------------
        |
        | If this payment was already successfully completed,
        | do not verify it again.
        |
        */

        if (
            $paymentModel->status === PaymentStatus::SUCCESSFUL
        ) {
            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=success'
                . '&payment=' . $paymentModel->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Get Gateway Callback Data
        |--------------------------------------------------------------------------
        */

        $authority = $request->query('Authority');
        $status = $request->query('Status');

        /*
        |--------------------------------------------------------------------------
        | 4. Validate Authority
        |--------------------------------------------------------------------------
        */

        if (! $authority) {
            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=error&reason=missing-authority'
            );
        }

        if (! $paymentModel->authority) {
            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=error&reason=missing-payment-authority'
            );
        }

        if (
            ! hash_equals(
                (string) $paymentModel->authority,
                (string) $authority
            )
        ) {
            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=error&reason=invalid-authority'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Check Gateway Payment Status
        |--------------------------------------------------------------------------
        */

        if (
            strtoupper((string) $status) !== 'OK'
        ) {
            $paymentModel = $this->paymentService->markAsCancelled(
                $paymentModel
            );

            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=cancelled'
                . '&payment=' . $paymentModel->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Verify Payment With Gateway
        |--------------------------------------------------------------------------
        */

        $result = $this->paymentGateway->verify(
            $paymentModel,
            (string) $authority
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Verification Failed
        |--------------------------------------------------------------------------
        */

        if (! $result['success']) {
            $paymentModel = $this->paymentService->markAsFailed(
                $paymentModel
            );

            return redirect(
                config('app.frontend_url')
                . '/payment/result?status=failed'
                . '&payment=' . $paymentModel->id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 8. Mark Payment As Successful
        |--------------------------------------------------------------------------
        */

        $paymentModel = $this->paymentService->markAsSuccessful(
            $paymentModel,
            $result['ref_id'] ?? null
        );

        /*
        |--------------------------------------------------------------------------
        | 9. Redirect To Frontend
        |--------------------------------------------------------------------------
        */

        return redirect(
            config('app.frontend_url')
            . '/payment/result?status=success'
            . '&payment=' . $paymentModel->id
        );
    }
}
