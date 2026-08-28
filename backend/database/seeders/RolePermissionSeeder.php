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
            /*
            |--------------------------------------------------------------------------
            | Projects
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Project Requests
            |--------------------------------------------------------------------------
            */

            [
                'name' => 'Create Project Requests',
                'slug' => 'project-requests.create',
            ],
            [
                'name' => 'View Own Project Requests',
                'slug' => 'project-requests.view',
            ],
            [
                'name' => 'Manage Project Requests',
                'slug' => 'project-requests.manage',
            ],
                [
    'name' => 'View Users',
    'slug' => 'users.view',
],
[
    'name' => 'Update Users',
    'slug' => 'users.update',
],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'slug' => $permission['slug'],
                ],
                $permission
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $customer = Role::updateOrCreate(
            [
                'slug' => 'customer',
            ],
            [
                'name' => 'Customer',
                'description' => 'Default customer role.',
            ]
        );

        $admin = Role::updateOrCreate(
            [
                'slug' => 'admin',
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrator role.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Customer Permissions
        |--------------------------------------------------------------------------
        |
        | Customer can:
        | - View public projects
        | - Create project requests
        | - View their own project requests
        |
        */

        $customerPermissions = Permission::query()
            ->whereIn('slug', [
                'projects.view',
                'project-requests.create',
                'project-requests.view',
            ])
            ->pluck('id');

        $customer->permissions()->sync(
            $customerPermissions
        );

        /*
        |--------------------------------------------------------------------------
        | Admin Permissions
        |--------------------------------------------------------------------------
        */

        $adminPermissions = Permission::query()
            ->pluck('id');

        $admin->permissions()->sync(
            $adminPermissions
        );
    }

}
