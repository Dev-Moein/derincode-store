<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService implements ProjectServiceInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->paginate($filters);
    }

    public function findBySlug(string $slug): ?Project
    {
        return $this->projectRepository->findBySlug($slug);
    }

    public function create(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            return $this->projectRepository->create($data);
        });
    }

    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            return $this->projectRepository->update(
                $project,
                $data
            );
        });
    }

    public function delete(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            return $this->projectRepository->delete($project);
        });
    }
}
