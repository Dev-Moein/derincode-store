<?php

namespace App\Contracts\Repositories;

use App\Models\Download;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DownloadRepositoryInterface
{
    public function create(array $data): Download;

    public function findById(int $id): ?Download;

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator;
}
