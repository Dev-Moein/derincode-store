<?php

namespace App\Contracts\Services;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

interface ProjectServiceInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;

    public function findBySlug(
        string $slug
    ): ?Project;

    public function create(
        array $data,
        ?UploadedFile $file = null
    ): Project;

    public function update(
        Project $project,
        array $data,
        ?UploadedFile $file = null
    ): Project;

    public function delete(
        Project $project
    ): bool;
}

