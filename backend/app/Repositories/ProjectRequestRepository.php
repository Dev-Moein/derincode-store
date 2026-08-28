<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectRequestRepositoryInterface;
use App\Models\ProjectRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRequestRepository implements ProjectRequestRepositoryInterface
{
    public function paginateForUser(
        int $userId,
        array $filters = []
    ): LengthAwarePaginator {
        $query = ProjectRequest::query()
            ->with('user')
            ->where('user_id', $userId);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest('id')
            ->paginate($filters['per_page'] ?? 12);
    }

    public function paginateForAdmin(
        array $filters = []
    ): LengthAwarePaginator {
        $query = ProjectRequest::query()
            ->with('user');

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest('id')
            ->paginate($filters['per_page'] ?? 12);
    }

    public function findById(int $id): ?ProjectRequest
    {
        return ProjectRequest::with('user')->find($id);
    }

    public function create(array $data): ProjectRequest
    {
        return ProjectRequest::create($data);
    }

    public function updateStatus(
        ProjectRequest $projectRequest,
        string $status,
        ?string $adminNote = null
    ): ProjectRequest {
        $projectRequest->update([
            'status' => $status,
            'admin_note' => $adminNote,
            'reviewed_at' => now(),
        ]);

        return $projectRequest->refresh();
    }
}
