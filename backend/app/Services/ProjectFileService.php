<?php

namespace App\Services;

use App\Contracts\Services\ProjectFileServiceInterface;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProjectFileService implements ProjectFileServiceInterface
{
    private const DISK = 'local';

    private const DIRECTORY = 'projects/files';

    private const MAX_SIZE_BYTES = 512 * 1024 * 1024;

    public function store(
        Project $project,
        UploadedFile $file
    ): Project {
        $this->validateFile($file);

        $path = $this->storeFile($project, $file);

        try {
            $project->update([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk(self::DISK)->delete($path);

            throw $exception;
        }

        return $project->fresh();
    }

    public function replace(
        Project $project,
        UploadedFile $file
    ): Project {
        $this->validateFile($file);

        $disk = Storage::disk(self::DISK);
        $oldPath = $project->file_path;
        $newPath = $this->storeFile($project, $file);

        try {
            $project->update([
                'file_path' => $newPath,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
            ]);
        } catch (\Throwable $exception) {
            $disk->delete($newPath);

            throw $exception;
        }

        // Delete only after the database points to the new file. If cleanup
        // fails, the old file remains harmlessly orphaned and can be cleaned
        // up later without breaking the active download.
        if ($oldPath && $oldPath !== $newPath) {
            $disk->delete($oldPath);
        }

        return $project->fresh();
    }

    public function delete(
        Project $project
    ): bool {
        $oldPath = $project->file_path;
        $project->update([
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
        ]);

        if (! $oldPath) {
            return true;
        }

        return Storage::disk(self::DISK)->delete($oldPath);
    }

    public function removeStoredFile(?string $path): bool
    {
        if (! $path) {
            return true;
        }

        return Storage::disk(self::DISK)->delete($path);
    }

    public function exists(
        Project $project
    ): bool {
        if (! $project->file_path) {
            return false;
        }

        return Storage::disk(self::DISK)
            ->exists($project->file_path);
    }

    public function getPath(
        Project $project
    ): ?string {
        if (! $project->file_path) {
            return null;
        }

        if (! $this->exists($project)) {
            return null;
        }

        return Storage::disk(self::DISK)
            ->path($project->file_path);
    }

    private function validateFile(UploadedFile $file): void
    {
        if (! $file->isValid()) {
            throw new RuntimeException(
                'The uploaded project file is invalid.'
            );
        }

        if ($file->getSize() > self::MAX_SIZE_BYTES) {
            throw new RuntimeException(
                'The uploaded project file is too large.'
            );
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $mimeType = strtolower(
            (string) $file->getMimeType()
        );

        $allowedMimeTypes = [
            'application/zip',
            'application/x-zip-compressed',
        ];

        if (
            $extension !== 'zip' ||
            ! in_array($mimeType, $allowedMimeTypes, true)
        ) {
            throw new RuntimeException(
                'Only valid ZIP files are allowed.'
            );
        }
    }

    private function storeFile(
        Project $project,
        UploadedFile $file
    ): string {
        $filename = sprintf(
            '%s-%s.zip',
            $project->id,
            bin2hex(random_bytes(16))
        );

        $path = $file->storeAs(
            self::DIRECTORY,
            $filename,
            self::DISK
        );

        if (! $path) {
            throw new RuntimeException(
                'Unable to store project file.'
            );
        }

        return $path;
    }
}
