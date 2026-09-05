<?php

namespace App\Contracts\Repositories;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function create(array $data): Payment;

    public function findById(int $id): ?Payment;

    public function findSuccessfulPayment(
        int $userId,
        int $projectId
    ): ?Payment;

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator;

    public function update(
        Payment $payment,
        array $data
    ): Payment;
    public function paginateSuccessfulForUser(
    int $userId,
    int $perPage = 15
): LengthAwarePaginator;
public function findByIdForUser(
    int $id,
    int $userId
): ?Payment;
}
