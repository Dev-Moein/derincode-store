<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminUserServiceInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminUserService implements AdminUserServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}


    public function paginate(
        array $filters = []
    ): LengthAwarePaginator {

        return $this->userRepository->paginateUsers(
            $filters
        );
    }


    public function find(
        int $id
    ): ?User {

        return $this->userRepository->findById(
            $id
        );
    }


    public function updateRole(
        User $user,
        string $role
    ): User {

        return DB::transaction(function () use (
            $user,
            $role
        ) {

            return $this->userRepository->updateRole(
                $user,
                $role
            );

        });
    }


    public function delete(
        User $user
    ): bool {

        return DB::transaction(function () use (
            $user
        ) {

            return $this->userRepository->delete(
                $user
            );

        });
    }
}
