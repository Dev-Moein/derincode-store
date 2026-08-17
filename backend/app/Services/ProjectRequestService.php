<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRequestRepositoryInterface;
use App\Contracts\Services\ProjectRequestServiceInterface;
use App\Models\ProjectRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectRequestService implements ProjectRequestServiceInterface
{
    public function __construct(
        private readonly ProjectRequestRepositoryInterface $repository,
    ) {}

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator {
        return $this->repository->paginateForUser(
            $userId,
            $filters
        );
    }

    public function paginateForAdmin(
        array $filters = []
    ): LengthAwarePaginator {
        return $this->repository->paginateForAdmin($filters);
    }

    public function findById(int $id): ?ProjectRequest
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): ProjectRequest
    {
        return DB::transaction(function () use ($data) {
            return $this->repository->create($data);
        });
    }

    public function updateStatus(
        ProjectRequest $projectRequest,
        string $status,
        ?string $adminNote = null
    ): ProjectRequest {
        return DB::transaction(function () use (
            $projectRequest,
            $status,
            $adminNote
        ) {
            return $this->repository->updateStatus(
                $projectRequest,
                $status,
                $adminNote
            );
        });
    }
}
