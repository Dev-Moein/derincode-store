<?php

namespace App\Services;

use App\Contracts\Repositories\PaymentRepositoryInterface;
use App\Contracts\Services\PaymentGatewayInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly PaymentGatewayInterface $paymentGateway,
    ) {}

   public function createForProject(
    User $user,
    int $projectId,
    string $gateway
): Payment {
    return DB::transaction(function () use (
        $user,
        $projectId,
        $gateway
    ) {
        $user = User::query()
            ->whereKey($user->id)
            ->lockForUpdate()
            ->firstOrFail();

        $project = Project::findOrFail($projectId);

        if (
            ! $project->is_for_sale ||
            $project->status->value !== 'published'
        ) {
            throw ValidationException::withMessages([
                'project' => [
                    'This project is not available for purchase.',
                ],
            ]);
        }

        if (
            $project->price === null ||
            $project->price <= 0
        ) {
            throw ValidationException::withMessages([
                'project' => [
                    'This project does not have a valid price.',
                ],
            ]);
        }

        $existingPayment = $this
            ->paymentRepository
            ->findSuccessfulPayment(
                $user->id,
                $project->id
            );

        if ($existingPayment) {
            throw ValidationException::withMessages([
                'project' => [
                    'You have already purchased this project.',
                ],
            ]);
        }

        $pendingPayment = Payment::query()
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->where('status', PaymentStatus::PENDING)
            ->latest('id')
            ->first();

        if ($pendingPayment) {
            return $pendingPayment;
        }

        return $this->paymentRepository->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => $project->price,
            'currency' => $project->currency,
            'gateway' => $gateway,
            'status' => PaymentStatus::PENDING,
        ]);
    });
}

    public function initiatePayment(
        Payment $payment,
        string $callbackUrl
    ): array {
        if ($payment->status !== PaymentStatus::PENDING) {
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment cannot be initiated.',
                ],
            ]);
        }

        $result = $this->paymentGateway->request(
            $payment,
            $callbackUrl
        );

        if (! $result['success']) {
            $this->markAsFailed($payment);

            return $result;
        }

        $payment = $this->paymentRepository->update(
            $payment,
            [
                'authority' => $result['authority'],
            ]
        );

        return [
            'success' => true,
            'payment' => $payment,
            'authority' => $result['authority'],
            'payment_url' => $result['payment_url'],
        ];
    }

    public function findById(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }

    public function hasPurchasedProject(
        int $userId,
        int $projectId
    ): bool {
        return $this->paymentRepository
            ->findSuccessfulPayment(
                $userId,
                $projectId
            ) !== null;
    }

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator {
        return $this->paymentRepository->paginateForUser(
            $userId,
            $filters
        );
    }

    public function markAsSuccessful(
    Payment $payment,
    ?string $transactionId = null
): Payment {
    return DB::transaction(function () use (
        $payment,
        $transactionId
    ) {
        $payment = Payment::query()
            ->whereKey($payment->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($payment->status === PaymentStatus::SUCCESSFUL) {
            return $payment;
        }

        if ($payment->status !== PaymentStatus::PENDING) {
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment cannot be marked as successful.',
                ],
            ]);
        }

        return $this->paymentRepository->update(
            $payment,
            [
                'status' => PaymentStatus::SUCCESSFUL,
                'transaction_id' => $transactionId,
                'paid_at' => now(),
            ]
        );
    });
}

    public function markAsFailed(
    Payment $payment
): Payment {
    return DB::transaction(function () use ($payment) {
        $payment = Payment::query()
            ->whereKey($payment->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($payment->status === PaymentStatus::FAILED) {
            return $payment;
        }

        if ($payment->status !== PaymentStatus::PENDING) {
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment cannot be marked as failed.',
                ],
            ]);
        }

        return $this->paymentRepository->update(
            $payment,
            [
                'status' => PaymentStatus::FAILED,
            ]
        );
    });
}

   public function markAsCancelled(
    Payment $payment
): Payment {
    return DB::transaction(function () use ($payment) {
        $payment = Payment::query()
            ->whereKey($payment->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($payment->status === PaymentStatus::CANCELLED) {
            return $payment;
        }

        if ($payment->status !== PaymentStatus::PENDING) {
            throw ValidationException::withMessages([
                'payment' => [
                    'This payment cannot be marked as cancelled.',
                ],
            ]);
        }

        return $this->paymentRepository->update(
            $payment,
            [
                'status' => PaymentStatus::CANCELLED,
            ]
        );
    });
}
    public function paginateSuccessfulForUser(
    int $userId,
    int $perPage = 15
): LengthAwarePaginator {
    return $this->paymentRepository->paginateSuccessfulForUser(
        $userId,
        $perPage
    );
}
public function findByIdForUser(
    int $id,
    int $userId
): ?Payment {
    return $this->paymentRepository
        ->findByIdForUser(
            $id,
            $userId
        );
}
public function findSuccessfulPayment(
    int $userId,
    int $projectId
): ?Payment {
    return $this->paymentRepository
        ->findSuccessfulPayment(
            userId: $userId,
            projectId: $projectId,
        );
}
}
