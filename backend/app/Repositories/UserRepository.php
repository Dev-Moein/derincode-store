<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function paginate(
        int $perPage = 15,
        ?string $search = null
    ): LengthAwarePaginator {
        return User::query()
            ->with('roles')
            ->when(
                $search,
                function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            )
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return User::query()
            ->with('roles')
            ->find($id);
    }

    public function update(
        User $user,
        array $data
    ): User {
        $user->update($data);

        return $user->fresh([
            'roles',
        ]);
    }
}
