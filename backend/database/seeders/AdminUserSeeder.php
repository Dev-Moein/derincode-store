<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            [
                'email' => 'admin@derincode.com',
            ],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123456'),
                'email_verified_at' => now(),
            ]
        );

        $role = Role::where('slug', 'admin')->first();

        if ($role) {
            $admin->roles()->syncWithoutDetaching([
                $role->id,
            ]);
        }
    }
}
