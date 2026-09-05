<?php

namespace App\Services;

use App\Contracts\Repositories\DownloadRepositoryInterface;
use App\Contracts\Services\DownloadServiceInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Models\Download;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadService implements DownloadServiceInterface
{
public function __construct(
    private readonly DownloadRepositoryInterface $downloadRepository,
    private readonly PaymentServiceInterface $paymentService,
) {}

    /**
     * Download a purchased project file.
     */
    public function download(
        User $user,
        int $projectId
    ): BinaryFileResponse {
        $project = Project::find($projectId);

        if (! $project) {
            throw ValidationException::withMessages([
                'project' => [
                    'Project not found.',
                ],
            ]);
        }

        if (! $project->file_path) {
            throw ValidationException::withMessages([
                'project' => [
                    'This project does not have a downloadable file.',
                ],
            ]);
        }

       $payment = $this->paymentService
    ->findSuccessfulPayment(
        userId: $user->id,
        projectId: $project->id,
    );

        if (! $payment) {
            throw ValidationException::withMessages([
                'project' => [
                    'You have not purchased this project.',
                ],
            ]);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($project->file_path)) {
            throw ValidationException::withMessages([
                'project' => [
                    'Project file is not available.',
                ],
            ]);
        }

        $filePath = $disk->path(
            $project->file_path
        );

        $this->createRecord(
            user: $user,
            projectId: $project->id,
            paymentId: $payment->id,
        );

        return response()->download(
            $filePath,
            $project->file_name ?: 'project.zip',
            [
                'Content-Type' => 'application/zip',
            ]
        );
    }

    /**
     * Create a download history record.
     */
    public function createRecord(
        User $user,
        int $projectId,
        int $paymentId
    ): Download {
        return $this->downloadRepository->create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'payment_id' => $paymentId,
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Check whether the user has successfully purchased the project.
     */
   public function hasPurchased(
    int $userId,
    int $projectId
): bool {
    return $this->paymentService
        ->hasPurchasedProject(
            userId: $userId,
            projectId: $projectId,
        );
}

    /**
     * Get authenticated user's download history.
     */
    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator {
        return $this->downloadRepository->paginateForUser(
            $userId,
            $filters
        );
    }
}
