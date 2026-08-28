<?php

namespace App\Repositories;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Project::query()
            ->with([
                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ]);

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($query) use ($search) {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere(
                        'short_description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if (! empty($filters['status'])) {
            $query->where(
                'status',
                $filters['status']
            );
        }

        if (isset($filters['is_featured'])) {
            $query->where(
                'is_featured',
                filter_var(
                    $filters['is_featured'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        if (isset($filters['is_for_sale'])) {
            $query->where(
                'is_for_sale',
                filter_var(
                    $filters['is_for_sale'],
                    FILTER_VALIDATE_BOOLEAN
                )
            );
        }

        return $query
            ->latest('published_at')
            ->latest('id')
            ->paginate(
                $filters['per_page'] ?? 12
            );
    }

    public function findById(int $id): ?Project
    {
        return Project::query()
            ->with([
                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->find($id);
    }

    public function findBySlug(string $slug): ?Project
    {
        return Project::query()
            ->with([
                'images' => fn ($query) => $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),
            ])
            ->where('slug', $slug)
            ->first();
    }

    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function update(
        Project $project,
        array $data
    ): Project {
        $project->update($data);

        return $project->refresh();
    }

    public function delete(Project $project): bool
    {
        return (bool) $project->delete();
    }
}
