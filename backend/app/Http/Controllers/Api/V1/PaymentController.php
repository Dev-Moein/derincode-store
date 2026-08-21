<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\PaymentGatewayInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePaymentRequest;
use App\Http\Resources\Api\V1\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $paymentService,
        private readonly PaymentGatewayInterface $paymentGateway,
    ) {}

    /**
     * Display authenticated user's payments.
     */
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

    /**
     * Create and initiate a payment for a project.
     */
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

        if (!$result['success']) {
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

    /**
     * Zarinpal payment callback.
     *
     * This route must NOT use auth:sanctum because
     * the payment gateway redirects the customer here.
     */
    public function callback(
        Request $request,
        int $payment
    ): JsonResponse {
        $paymentModel = $this->paymentService->findById($payment);

        if (!$paymentModel) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
                'data' => null,
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Already successful
        |--------------------------------------------------------------------------
        */

        if ($paymentModel->status === PaymentStatus::SUCCESSFUL) {
            $paymentModel->load('project');

            return response()->json([
                'success' => true,
                'message' => 'Payment has already been verified.',
                'data' => new PaymentResource($paymentModel),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get gateway callback parameters
        |--------------------------------------------------------------------------
        */

        $authority = $request->query('Authority');
        $status = $request->query('Status');

        if (!$authority) {
            return response()->json([
                'success' => false,
                'message' => 'Payment authority is missing.',
                'data' => null,
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Customer cancelled payment
        |--------------------------------------------------------------------------
        */

        if (strtoupper((string) $status) !== 'OK') {
            $paymentModel = $this->paymentService->markAsCancelled(
                $paymentModel
            );

            $paymentModel->load('project');

            return response()->json([
                'success' => false,
                'message' => 'Payment was cancelled.',
                'data' => new PaymentResource($paymentModel),
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify payment with gateway
        |--------------------------------------------------------------------------
        */

        $result = $this->paymentGateway->verify(
            $paymentModel,
            (string) $authority
        );

        if (!$result['success']) {
            $paymentModel = $this->paymentService->markAsFailed(
                $paymentModel
            );

            $paymentModel->load('project');

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.',
                'data' => [
                    'payment' => new PaymentResource($paymentModel),
                    'code' => $result['code'] ?? null,
                ],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Mark payment as successful
        |--------------------------------------------------------------------------
        */

        $paymentModel = $this->paymentService->markAsSuccessful(
            $paymentModel,
            $result['ref_id'] ?? null
        );

        $paymentModel->load('project');

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully.',
            'data' => [
                'payment' => new PaymentResource($paymentModel),
                'ref_id' => $result['ref_id'] ?? null,
            ],
        ]);
    }

    /**
     * Display a single payment belonging to authenticated user.
     */
    public function show(
        Request $request,
        int $id
    ): JsonResponse {
        $payment = $this->paymentService->findById($id);

        if (
            !$payment ||
            $payment->user_id !== $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
                'data' => null,
            ], 404);
        }

        $payment->load('project');

        return response()->json([
            'success' => true,
            'message' => 'Payment retrieved successfully.',
            'data' => new PaymentResource($payment),
        ]);
    }
}
