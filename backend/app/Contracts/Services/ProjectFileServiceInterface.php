<?php

namespace App\Contracts\Services;

use App\Models\Project;
use Illuminate\Http\UploadedFile;

interface ProjectFileServiceInterface
{
    public function store(
        Project $project,
        UploadedFile $file
    ): Project;

    public function replace(
        Project $project,
        UploadedFile $file
    ): Project;

    public function delete(
        Project $project
    ): bool;

    public function removeStoredFile(?string $path): bool;

    public function exists(
        Project $project
    ): bool;

    public function getPath(
        Project $project
    ): ?string;
}
