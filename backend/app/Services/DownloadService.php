<?php

namespace App\Services;

use App\Contracts\Services\DownloadServiceInterface;
use App\Models\Download;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadService implements DownloadServiceInterface
{
    public function download(
        User $user,
        int $projectId
    ): BinaryFileResponse {
        $project = Project::find($projectId);

        if (!$project) {
            throw ValidationException::withMessages([
                'project' => [
                    'Project not found.',
                ],
            ]);
        }

        if (!$project->file_path) {
            throw ValidationException::withMessages([
                'project' => [
                    'This project does not have a downloadable file.',
                ],
            ]);
        }

        $payment = Payment::query()
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->where('status', 'successful')
            ->latest('id')
            ->first();

        if (!$payment) {
            throw ValidationException::withMessages([
                'project' => [
                    'You have not purchased this project.',
                ],
            ]);
        }

        $disk = Storage::disk('local');

        if (!$disk->exists($project->file_path)) {
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

    public function createRecord(
        User $user,
        int $projectId,
        int $paymentId
    ): Download {
        return Download::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'payment_id' => $paymentId,
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function hasPurchased(
        int $userId,
        int $projectId
    ): bool {
        return Payment::query()
            ->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->where('status', 'successful')
            ->exists();
    }
}
