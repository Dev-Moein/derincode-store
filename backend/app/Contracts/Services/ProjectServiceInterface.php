<?php

namespace App\Contracts\Services;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectServiceInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Project;

    public function create(array $data): Project;

    public function update(Project $project, array $data): Project;

    public function delete(Project $project): bool;
}
