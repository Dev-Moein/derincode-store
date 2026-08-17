<?php

namespace App\Contracts\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\UploadedFile;

interface ProjectImageServiceInterface
{
    public function store(
        Project $project,
        UploadedFile $file,
        ?string $alt = null,
        int $sortOrder = 0
    ): ProjectImage;

    public function reorder(
        Project $project,
        array $images
    ): void;

    public function delete(ProjectImage $image): bool;
}
