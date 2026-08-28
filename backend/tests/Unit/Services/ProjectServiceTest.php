<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Services\ProjectFileServiceInterface;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Mockery;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{
    public function test_it_can_paginate_projects(): void
    {
        $filters = [
            'status' => 'published',
            'per_page' => 10,
        ];

        $paginator = Mockery::mock(
            LengthAwarePaginator::class
        );

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('paginate')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->paginate($filters);

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_paginate_projects_without_filters(): void
    {
        $paginator = Mockery::mock(
            LengthAwarePaginator::class
        );

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('paginate')
            ->once()
            ->with([])
            ->andReturn($paginator);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->paginate();

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_find_project_by_slug(): void
    {
        $project = new Project;

        $project->id = 1;
        $project->slug = 'my-project';

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with('my-project')
            ->andReturn($project);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->findBySlug(
            'my-project'
        );

        $this->assertSame(
            $project,
            $result
        );
    }

    public function test_it_returns_null_when_project_slug_is_not_found(): void
    {
        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('findBySlug')
            ->once()
            ->with('unknown-project')
            ->andReturn(null);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->findBySlug(
            'unknown-project'
        );

        $this->assertNull($result);
    }

    public function test_it_creates_project_without_file(): void
    {
        $data = [
            'title' => 'Test Project',
            'slug' => 'test-project',
        ];

        $project = new Project;

        $project->id = 1;
        $project->title = 'Test Project';

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($project);

        $fileService
            ->shouldNotReceive('store');

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->create($data);

        $this->assertSame(
            $project,
            $result
        );
    }

    public function test_it_removes_file_from_data_before_creating_project(): void
    {
        $file = UploadedFile::fake()->create(
            'project.zip',
            100,
            'application/zip'
        );

        $data = [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'file' => $file,
        ];

        $project = new Project;

        $project->id = 1;

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('create')
            ->once()
            ->with([
                'title' => 'Test Project',
                'slug' => 'test-project',
            ])
            ->andReturn($project);

        $fileService
            ->shouldReceive('store')
            ->once()
            ->with(
                $project,
                $file
            )
            ->andReturn($project);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->create(
            $data,
            $file
        );

        $this->assertSame(
            $project,
            $result
        );
    }

    public function test_it_creates_project_with_file(): void
    {
        $data = [
            'title' => 'Test Project',
            'slug' => 'test-project',
        ];

        $file = UploadedFile::fake()->create(
            'project.zip',
            100,
            'application/zip'
        );

        $project = new Project;

        $project->id = 1;

        $storedProject = new Project;

        $storedProject->id = 1;
        $storedProject->file_path = 'projects/project.zip';

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($project);

        $fileService
            ->shouldReceive('store')
            ->once()
            ->with(
                $project,
                $file
            )
            ->andReturn($storedProject);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->create(
            $data,
            $file
        );

        $this->assertSame(
            $storedProject,
            $result
        );
    }

    public function test_it_updates_project_without_file(): void
    {
        $project = new Project;

        $project->id = 1;

        $data = [
            'title' => 'Updated Project',
        ];

        $updatedProject = new Project;

        $updatedProject->id = 1;
        $updatedProject->title = 'Updated Project';

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('update')
            ->once()
            ->with(
                $project,
                $data
            )
            ->andReturn($updatedProject);

        $fileService
            ->shouldNotReceive('replace');

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->update(
            $project,
            $data
        );

        $this->assertSame(
            $updatedProject,
            $result
        );
    }

    public function test_it_updates_project_with_file(): void
    {
        $project = new Project;

        $project->id = 1;

        $data = [
            'title' => 'Updated Project',
        ];

        $file = UploadedFile::fake()->create(
            'new-project.zip',
            100,
            'application/zip'
        );

        $updatedProject = new Project;

        $updatedProject->id = 1;
        $updatedProject->title = 'Updated Project';

        $projectWithNewFile = new Project;

        $projectWithNewFile->id = 1;
        $projectWithNewFile->file_path =
            'projects/new-project.zip';

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('update')
            ->once()
            ->with(
                $project,
                $data
            )
            ->andReturn($updatedProject);

        $fileService
            ->shouldReceive('replace')
            ->once()
            ->with(
                $updatedProject,
                $file
            )
            ->andReturn($projectWithNewFile);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->update(
            $project,
            $data,
            $file
        );

        $this->assertSame(
            $projectWithNewFile,
            $result
        );
    }

    public function test_it_removes_file_from_data_before_updating_project(): void
    {
        $project = new Project;

        $project->id = 1;

        $file = UploadedFile::fake()->create(
            'project.zip',
            100,
            'application/zip'
        );

        $data = [
            'title' => 'Updated Project',
            'file' => $file,
        ];

        $updatedProject = new Project;

        $updatedProject->id = 1;

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $repository
            ->shouldReceive('update')
            ->once()
            ->with(
                $project,
                [
                    'title' => 'Updated Project',
                ]
            )
            ->andReturn($updatedProject);

        $fileService
            ->shouldReceive('replace')
            ->once()
            ->with(
                $updatedProject,
                $file
            )
            ->andReturn($updatedProject);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->update(
            $project,
            $data,
            $file
        );

        $this->assertSame(
            $updatedProject,
            $result
        );
    }

    public function test_it_deletes_project_file_and_project(): void
    {
        $project = new Project;

        $project->id = 1;

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $fileService
            ->shouldReceive('delete')
            ->once()
            ->with($project)
            ->ordered();

        $repository
            ->shouldReceive('delete')
            ->once()
            ->with($project)
            ->ordered()
            ->andReturn(true);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->delete($project);

        $this->assertTrue($result);
    }

    public function test_it_returns_false_when_project_deletion_fails(): void
    {
        $project = new Project;

        $project->id = 1;

        $repository = Mockery::mock(
            ProjectRepositoryInterface::class
        );

        $fileService = Mockery::mock(
            ProjectFileServiceInterface::class
        );

        $fileService
            ->shouldReceive('delete')
            ->once()
            ->with($project);

        $repository
            ->shouldReceive('delete')
            ->once()
            ->with($project)
            ->andReturn(false);

        $service = new ProjectService(
            $repository,
            $fileService
        );

        $result = $service->delete($project);

        $this->assertFalse($result);
    }
}
