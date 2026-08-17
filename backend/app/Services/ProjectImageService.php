<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectImageRepositoryInterface;
use App\Contracts\Services\ProjectImageServiceInterface;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectImageService implements ProjectImageServiceInterface
{
    public function __construct(
        private readonly ProjectImageRepositoryInterface $imageRepository,
    ) {}

    public function store(
        Project $project,
        UploadedFile $file,
        ?string $alt = null,
        int $sortOrder = 0
    ): ProjectImage {
        return DB::transaction(function () use (
            $project,
            $file,
            $alt,
            $sortOrder
        ) {
            $path = $file->store(
                "projects/{$project->id}/images",
                'public'
            );

            return $this->imageRepository->create([
                'project_id' => $project->id,
                'path' => $path,
                'alt' => $alt,
                'sort_order' => $sortOrder,
            ]);
        });
    }

    public function reorder(
        Project $project,
        array $images
    ): void {
        DB::transaction(function () use ($project, $images) {

            foreach ($images as $imageData) {
                $image = $project->images()
                    ->whereKey($imageData['id'])
                    ->first();

                if (!$image) {
                    continue;
                }

                $this->imageRepository->updateSortOrder(
                    $image,
                    (int) $imageData['sort_order']
                );
            }
        });
    }

    public function delete(ProjectImage $image): bool
    {
        return DB::transaction(function () use ($image) {
            if ($image->path) {
                Storage::disk('public')->delete($image->path);
            }

            return $this->imageRepository->delete($image);
        });
    }
}
