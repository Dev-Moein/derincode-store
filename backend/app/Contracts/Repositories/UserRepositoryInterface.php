<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function paginate(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function update(
        User $user,
        array $data
    ): User;
}
