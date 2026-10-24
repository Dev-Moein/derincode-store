<?php

namespace App\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectFileServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

            if (blank($data['slug'] ?? null)) {
                $baseSlug = Str::slug($data['title']);

                if ($baseSlug === '') {
                    $baseSlug = 'project';
                }

                $slug = $baseSlug;
                $suffix = 2;

                while (Project::query()->where('slug', $slug)->exists()) {
                    $suffixText = '-'.$suffix;

                    $slug = substr(
                        $baseSlug,
                        0,
                        255 - strlen($suffixText)
                    ).$suffixText;

                    $suffix++;
                }

                $data['slug'] = $slug;
            }
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

    public function delete(Project $project): bool
    {
        $hasPayments = DB::table('payments')
            ->where('project_id', $project->id)
            ->exists();

        if ($hasPayments) {
            throw new \RuntimeException(
                'این پروژه دارای سوابق پرداخت است و امکان حذف فیزیکی آن وجود ندارد.'
            );
        }

        $filePath = $project->file_path;

        $deleted = DB::transaction(function () use ($project) {
            return $this->projectRepository->delete($project);
        });

        if ($deleted) {
            $this->projectFileService->removeStoredFile($filePath);
        }

        return $deleted;
    }
}
