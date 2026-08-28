<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\ProjectRequestRepositoryInterface;
use App\Models\ProjectRequest;
use App\Services\ProjectRequestService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class ProjectRequestServiceTest extends TestCase
{
    private ProjectRequestRepositoryInterface $repository;

    private ProjectRequestService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(
            ProjectRequestRepositoryInterface::class
        );

        $this->service = new ProjectRequestService(
            $this->repository
        );
    }

    public function test_it_can_paginate_project_requests_for_user(): void
    {
        $userId = 10;

        $filters = [
            'status' => 'pending',
            'per_page' => 15,
        ];

        $paginator = Mockery::mock(
            LengthAwarePaginator::class
        );

        $this->repository
            ->shouldReceive('paginateForUser')
            ->once()
            ->with($userId, $filters)
            ->andReturn($paginator);

        $result = $this->service->paginateForUser(
            $userId,
            $filters
        );

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_paginate_project_requests_for_user_without_filters(): void
    {
        $userId = 10;

        $paginator = Mockery::mock(
            LengthAwarePaginator::class
        );

        $this->repository
            ->shouldReceive('paginateForUser')
            ->once()
            ->with($userId, [])
            ->andReturn($paginator);

        $result = $this->service->paginateForUser(
            $userId
        );

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_paginate_project_requests_for_admin(): void
    {
        $filters = [
            'status' => 'pending',
            'search' => 'Laravel',
            'per_page' => 20,
        ];

        $paginator = Mockery::mock(
            LengthAwarePaginator::class
        );

        $this->repository
            ->shouldReceive('paginateForAdmin')
            ->once()
            ->with($filters)
            ->andReturn($paginator);

        $result = $this->service->paginateForAdmin(
            $filters
        );

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_paginate_project_requests_for_admin_without_filters(): void
    {
        $paginator = Mockery::mock(
            LengthAwarePaginator::class
        );

        $this->repository
            ->shouldReceive('paginateForAdmin')
            ->once()
            ->with([])
            ->andReturn($paginator);

        $result = $this->service->paginateForAdmin();

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_find_project_request_by_id(): void
    {
        $projectRequest = new ProjectRequest([
            'id' => 1,
        ]);

        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(1)
            ->andReturn($projectRequest);

        $result = $this->service->findById(1);

        $this->assertSame(
            $projectRequest,
            $result
        );
    }

    public function test_it_returns_null_when_project_request_is_not_found(): void
    {
        $this->repository
            ->shouldReceive('findById')
            ->once()
            ->with(999)
            ->andReturnNull();

        $result = $this->service->findById(999);

        $this->assertNull(
            $result
        );
    }

    public function test_it_can_create_project_request(): void
    {
        $data = [
            'user_id' => 1,
            'title' => 'Build Laravel API',
            'description' => 'I need a Laravel API.',
            'status' => 'pending',
        ];

        $projectRequest = new ProjectRequest(
            $data
        );

        $this->repository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($projectRequest);

        $result = $this->service->create(
            $data
        );

        $this->assertSame(
            $projectRequest,
            $result
        );
    }

    public function test_it_can_update_project_request_status(): void
    {
        $projectRequest = new ProjectRequest([
            'id' => 1,
            'status' => 'pending',
        ]);

        $updatedProjectRequest = new ProjectRequest([
            'id' => 1,
            'status' => 'accepted',
            'admin_note' => 'Your request has been accepted.',
        ]);

        $this->repository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(
                $projectRequest,
                'accepted',
                'Your request has been accepted.'
            )
            ->andReturn($updatedProjectRequest);

        $result = $this->service->updateStatus(
            $projectRequest,
            'accepted',
            'Your request has been accepted.'
        );

        $this->assertSame(
            $updatedProjectRequest,
            $result
        );
    }

    public function test_it_can_update_project_request_status_without_admin_note(): void
    {
        $projectRequest = new ProjectRequest([
            'id' => 1,
            'status' => 'pending',
        ]);

        $updatedProjectRequest = new ProjectRequest([
            'id' => 1,
            'status' => 'completed',
        ]);

        $this->repository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(
                $projectRequest,
                'completed',
                null
            )
            ->andReturn($updatedProjectRequest);

        $result = $this->service->updateStatus(
            $projectRequest,
            'completed'
        );

        $this->assertSame(
            $updatedProjectRequest,
            $result
        );
    }
}
