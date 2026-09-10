<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\User;

interface AdminUserServiceInterface
{
    public function paginate(
        array $filters = []
    ): LengthAwarePaginator;


    public function find(
        int $id
    ): ?User;


    public function updateRole(
        User $user,
        string $role
    ): User;


    public function delete(
        User $user
    ): bool;
}
