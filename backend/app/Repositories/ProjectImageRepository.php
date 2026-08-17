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

    public function updateSortOrder(
        ProjectImage $image,
        int $sortOrder
    ): ProjectImage {
        $image->update([
            'sort_order' => $sortOrder,
        ]);

        return $image->refresh();
    }

    public function delete(ProjectImage $image): bool
    {
        return (bool) $image->delete();
    }
}
