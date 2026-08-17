<?php

namespace App\Contracts\Repositories;

use App\Models\ProjectRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRequestRepositoryInterface
{
    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator;

    public function paginateForAdmin(
        array $filters = []
    ): LengthAwarePaginator;

    public function findById(int $id): ?ProjectRequest;

    public function create(array $data): ProjectRequest;

    public function updateStatus(
        ProjectRequest $projectRequest,
        string $status,
        ?string $adminNote = null
    ): ProjectRequest;
}
