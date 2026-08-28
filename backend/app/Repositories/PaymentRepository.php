<?php

namespace App\Repositories;

use App\Contracts\Repositories\PaymentRepositoryInterface;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function findById(int $id): ?Payment
    {
        return Payment::with([
            'project',
        ])->find($id);
    }

    public function findSuccessfulPayment(
        int $userId,
        int $projectId
    ): ?Payment {
        return Payment::query()
            ->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->where(
                'status',
                PaymentStatus::SUCCESSFUL
            )
            ->first();
    }

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator {
        return Payment::query()
            ->with('project')
            ->where('user_id', $userId)
            ->when(
                ! empty($filters['status']),
                fn ($query) => $query->where(
                    'status',
                    $filters['status']
                )
            )
            ->latest('id')
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    public function update(
        Payment $payment,
        array $data
    ): Payment {
        $payment->update($data);

        return $payment->refresh();
    }
}
