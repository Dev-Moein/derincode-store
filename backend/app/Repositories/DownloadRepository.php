<?php

namespace App\Repositories;

use App\Contracts\Repositories\DownloadRepositoryInterface;
use App\Models\Download;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DownloadRepository implements DownloadRepositoryInterface
{
    public function create(array $data): Download
    {
        return Download::create($data);
    }

    public function findById(int $id): ?Download
    {
        return Download::with([
            'project',
            'payment',
        ])->find($id);
    }

    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator {
        $query = Download::query()
            ->where('user_id', $userId)
            ->with([
                'project',
                'payment',
            ])
            ->latest('downloaded_at');

        if (!empty($filters['project_id'])) {
            $query->where(
                'project_id',
                $filters['project_id']
            );
        }

        $perPage = min(
            max((int) ($filters['per_page'] ?? 15), 1),
            100
        );

        return $query->paginate($perPage);
    }
}
