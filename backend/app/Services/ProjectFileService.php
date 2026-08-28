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

    public function store(
        Project $project,
        UploadedFile $file
    ): Project {
        if (! $file->isValid()) {
            throw new RuntimeException(
                'The uploaded project file is invalid.'
            );
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        if ($extension !== 'zip') {
            throw new RuntimeException(
                'Only ZIP files are allowed.'
            );
        }

        $filename = sprintf(
            '%s-%s.zip',
            $project->id,
            uniqid('', true)
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

        $project->update([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return $project->fresh();
    }

    public function replace(
        Project $project,
        UploadedFile $file
    ): Project {
        $this->deleteFile($project);

        return $this->store(
            $project,
            $file
        );
    }

    public function delete(
        Project $project
    ): bool {
        $deleted = true;

        if ($project->file_path) {
            $deleted = $this->deleteFile($project);
        }

        $project->update([
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
        ]);

        return $deleted;
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

    private function deleteFile(
        Project $project
    ): bool {
        if (! $project->file_path) {
            return true;
        }

        return Storage::disk(self::DISK)
            ->delete($project->file_path);
    }
}
