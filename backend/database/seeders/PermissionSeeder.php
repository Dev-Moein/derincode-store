<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'View Dashboard',
                'slug' => 'dashboard.view',
                'description' => 'View admin dashboard.',
            ],

            [
                'name' => 'View Users',
                'slug' => 'users.view',
                'description' => 'View users in admin panel.',
            ],

            [
                'name' => 'Update Users',
                'slug' => 'users.update',
                'description' => 'Update users in admin panel.',
            ],

            [
                'name' => 'Create Projects',
                'slug' => 'projects.create',
                'description' => 'Create projects.',
            ],

            [
                'name' => 'Update Projects',
                'slug' => 'projects.update',
                'description' => 'Update projects.',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
                'description' => 'Delete Users',
            ],
            [
                'name' => 'Delete Projects',
                'slug' => 'projects.delete',
                'description' => 'Delete projects.',
            ],

            [
                'name' => 'Manage Project Requests',
                'slug' => 'project-requests.manage',
                'description' => 'Manage project requests.',
            ],

            [
                'name' => 'View Payments',
                'slug' => 'payments.view',
                'description' => 'View payments.',
            ],

            [
                'name' => 'View Downloads',
                'slug' => 'downloads.view',
                'description' => 'View download history.',
            ],
        ];

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator role.',
            ]
        );

        foreach ($permissions as $permissionData) {
            $permission = Permission::updateOrCreate(
                [
                    'slug' => $permissionData['slug'],
                ],
                [
                    'name' => $permissionData['name'],
                    'description' => $permissionData['description'],
                ]
            );

            $adminRole->permissions()->syncWithoutDetaching([
                $permission->id,
            ]);
        }
    }
}
