<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Role;
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
        ->with([
            'roles.permissions'
        ])
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


    /*
    |--------------------------------------------------------------------------
    | Admin Users
    |--------------------------------------------------------------------------
    */

    public function paginateUsers(
        array $filters = []
    ): LengthAwarePaginator {

        return $this->paginate(
            $filters['per_page'] ?? 12,
            $filters['search'] ?? null
        );
    }

public function findById(int $id): ?User
{
    return User::query()
        ->with([
            'roles.permissions'
        ])
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


    public function updateRole(
    User $user,
    string $role
): User {

    $roleModel = Role::where('name', $role)
        ->orWhere('slug', $role)
        ->firstOrFail();


    $user->roles()->sync([
        $roleModel->id
    ]);


    return $user->load([
        'roles.permissions'
    ]);
}


public function delete(
    User $user
): bool {

    return $user->delete();
}
}
