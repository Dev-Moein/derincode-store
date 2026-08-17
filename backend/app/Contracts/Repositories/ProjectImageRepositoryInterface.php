<?php

namespace App\Contracts\Repositories;

use App\Models\Project;
use App\Models\ProjectImage;

interface ProjectImageRepositoryInterface
{
    public function create(array $data): ProjectImage;

    public function findById(int $id): ?ProjectImage;

    public function updateSortOrder(
        ProjectImage $image,
        int $sortOrder
    ): ProjectImage;

    public function delete(ProjectImage $image): bool;
}
