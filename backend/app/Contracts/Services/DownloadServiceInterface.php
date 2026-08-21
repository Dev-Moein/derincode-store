<?php

namespace App\Contracts\Services;

use App\Models\Download;
use App\Models\User;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface DownloadServiceInterface
{
    public function download(
        User $user,
        int $projectId
    ): BinaryFileResponse;

    public function createRecord(
        User $user,
        int $projectId,
        int $paymentId
    ): Download;

    public function hasPurchased(
        int $userId,
        int $projectId
    ): bool;
}
