<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectFileServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProjectService implements ProjectServiceInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectFileServiceInterface $projectFileService,
    ) {}

    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->projectRepository->paginate(
            $filters
        );
    }

    public function findBySlug(
        string $slug
    ): ?Project {
        return $this->projectRepository->findBySlug(
            $slug
        );
    }

    public function create(
        array $data,
        ?UploadedFile $file = null
    ): Project {
        return DB::transaction(function () use (
            $data,
            $file
        ) {
            unset($data['file']);

            $project = $this->projectRepository->create(
                $data
            );

            if ($file) {
                $project = $this->projectFileService->store(
                    $project,
                    $file
                );
            }

            return $project;
        });
    }

    public function update(
        Project $project,
        array $data,
        ?UploadedFile $file = null
    ): Project {
        return DB::transaction(function () use (
            $project,
            $data,
            $file
        ) {
            unset($data['file']);

            $project = $this->projectRepository->update(
                $project,
                $data
            );

            if ($file) {
                $project = $this->projectFileService->replace(
                    $project,
                    $file
                );
            }

            return $project;
        });
    }

    public function delete(
        Project $project
    ): bool {
        return DB::transaction(function () use (
            $project
        ) {
            $this->projectFileService->delete(
                $project
            );

            return $this->projectRepository->delete(
                $project
            );
        });
    }
}
