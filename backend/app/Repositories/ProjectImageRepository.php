<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectImageRepositoryInterface;
use App\Models\ProjectImage;

class ProjectImageRepository implements ProjectImageRepositoryInterface
{
    public function create(array $data): ProjectImage
    {
        return ProjectImage::create($data);
    }

    public function findById(int $id): ?ProjectImage
    {
        return ProjectImage::find($id);
    }

    public function delete(ProjectImage $image): bool
    {
        return (bool) $image->delete();
    }
}
