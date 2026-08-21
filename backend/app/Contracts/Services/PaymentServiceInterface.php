<?php

namespace App\Contracts\Services;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentServiceInterface
{
    public function createForProject(
        User $user,
        int $projectId,
        string $gateway
    ): Payment;

    public function initiatePayment(
        Payment $payment,
        string $callbackUrl
    ): array;

    public function findById(int $id): ?Payment;

    public function hasPurchasedProject(
        int $userId,
        int $projectId
    ): bool;

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator;

    public function markAsSuccessful(
        Payment $payment,
        ?string $transactionId = null
    ): Payment;

    public function markAsFailed(
        Payment $payment
    ): Payment;

    public function markAsCancelled(
        Payment $payment
    ): Payment;
}
