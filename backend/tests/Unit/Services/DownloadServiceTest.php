<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\DownloadRepositoryInterface;
use App\Contracts\Services\PaymentServiceInterface;
use App\Models\Download;
use App\Models\User;
use App\Services\DownloadService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class DownloadServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_creates_download_record(): void
    {
        $user = new User;

        $user->id = 1;

        $download = new Download([
            'user_id' => 1,
            'project_id' => 10,
            'payment_id' => 20,
        ]);

        $repository = Mockery::mock(
            DownloadRepositoryInterface::class
        );

        $paymentService = Mockery::mock(
            PaymentServiceInterface::class
        );

        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) {
                return
                    $data['user_id'] === 1 &&
                    $data['project_id'] === 10 &&
                    $data['payment_id'] === 20 &&
                    array_key_exists(
                        'downloaded_at',
                        $data
                    ) &&
                    array_key_exists(
                        'ip_address',
                        $data
                    ) &&
                    array_key_exists(
                        'user_agent',
                        $data
                    );
            }))
            ->andReturn($download);

        $service = new DownloadService(
            $repository,
            $paymentService
        );

        $result = $service->createRecord(
            user: $user,
            projectId: 10,
            paymentId: 20,
        );

        $this->assertSame(
            $download,
            $result
        );
    }

    public function test_it_can_paginate_user_downloads(): void
    {
        $userId = 1;

        $filters = [
            'project_id' => 10,
            'per_page' => 15,
        ];

        $paginator = new LengthAwarePaginator(
            [],
            0,
            15,
            1
        );

        $repository = Mockery::mock(
            DownloadRepositoryInterface::class
        );

        $paymentService = Mockery::mock(
            PaymentServiceInterface::class
        );

        $repository
            ->shouldReceive('paginateForUser')
            ->once()
            ->with(
                $userId,
                $filters
            )
            ->andReturn($paginator);

        $service = new DownloadService(
            $repository,
            $paymentService
        );

        $result = $service->paginateForUser(
            $userId,
            $filters
        );

        $this->assertSame(
            $paginator,
            $result
        );
    }

    public function test_it_can_paginate_user_downloads_without_filters(): void
    {
        $userId = 1;

        $paginator = new LengthAwarePaginator(
            [],
            0,
            15,
            1
        );

        $repository = Mockery::mock(
            DownloadRepositoryInterface::class
        );

        $paymentService = Mockery::mock(
            PaymentServiceInterface::class
        );

        $repository
            ->shouldReceive('paginateForUser')
            ->once()
            ->with(
                $userId,
                []
            )
            ->andReturn($paginator);

        $service = new DownloadService(
            $repository,
            $paymentService
        );

        $result = $service->paginateForUser(
            $userId
        );

        $this->assertSame(
            $paginator,
            $result
        );
    }
}

