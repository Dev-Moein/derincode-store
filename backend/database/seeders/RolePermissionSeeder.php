<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'View Projects',
                'slug' => 'projects.view',
            ],
            [
                'name' => 'Create Projects',
                'slug' => 'projects.create',
            ],
            [
                'name' => 'Update Projects',
                'slug' => 'projects.update',
            ],
            [
                'name' => 'Delete Projects',
                'slug' => 'projects.delete',
            ],
            [
                'name' => 'View Users',
                'slug' => 'users.view',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
            ],
            [
                'name' => 'Update Users',
                'slug' => 'users.update',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
            ],
            [
                'name' => 'View Project Requests',
                'slug' => 'project-requests.view',
            ],
            [
                'name' => 'Manage Project Requests',
                'slug' => 'project-requests.manage',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $customer = Role::updateOrCreate(
            ['slug' => 'customer'],
            [
                'name' => 'Customer',
                'description' => 'Default customer role.',
            ]
        );

        $admin = Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator role.',
            ]
        );

        $customerPermissions = Permission::whereIn('slug', [
            'projects.view',
            'project-requests.view',
        ])->get();

        $adminPermissions = Permission::all();

        $customer->permissions()->sync(
            $customerPermissions->pluck('id')
        );

        $admin->permissions()->sync(
            $adminPermissions->pluck('id')
        );
    }
}
