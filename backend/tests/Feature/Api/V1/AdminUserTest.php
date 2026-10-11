<?php

namespace Tests\Feature\Api\V1;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->admin = User::factory()->create();

        $adminRole = Role::query()
            ->where('slug', 'admin')
            ->firstOrFail();

        $this->admin->roles()->attach($adminRole);
    }

    public function test_admin_can_list_users(): void
    {
        User::factory()
            ->count(3)
            ->create();

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/admin/users');

        $response
            ->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonPath('meta.total', 4);
    }

    public function test_unauthenticated_user_cannot_list_users(): void
    {
        $response = $this->getJson(
            '/api/v1/admin/users'
        );

        $response
            ->assertUnauthorized();
    }

    public function test_customer_cannot_list_users(): void
    {
        $customer = User::factory()->create();

        $customerRole = Role::query()
            ->where('slug', 'customer')
            ->firstOrFail();

        $customer->roles()->attach($customerRole);

        $response = $this
            ->actingAs($customer, 'sanctum')
            ->getJson('/api/v1/admin/users');

        $response
            ->assertForbidden();
    }

    public function test_admin_can_search_users(): void
    {
        User::factory()->create([
            'name' => 'Ali Test User',
            'email' => 'ali@example.com',
        ]);

        User::factory()->create([
            'name' => 'Sara Test User',
            'email' => 'sara@example.com',
        ]);

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->getJson(
                '/api/v1/admin/users?search=ali@example.com'
            );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.0.email',
                'ali@example.com'
            )
            ->assertJsonCount(
                1,
                'data'
            );
    }

    public function test_admin_can_view_user(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->getJson(
                "/api/v1/admin/users/{$user->id}"
            );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User retrieved successfully.',
            ])
            ->assertJsonPath(
                'data.id',
                $user->id
            );
    }

    public function test_admin_gets_404_for_unknown_user(): void
    {
        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->getJson(
                '/api/v1/admin/users/999999'
            );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'User not found.',
            ]);
    }

    public function test_customer_cannot_view_user(): void
    {
        $customer = User::factory()->create();
        $user = User::factory()->create();

        $customerRole = Role::query()
            ->where('slug', 'customer')
            ->firstOrFail();

        $customer->roles()->attach($customerRole);

        $response = $this
            ->actingAs($customer, 'sanctum')
            ->getJson(
                "/api/v1/admin/users/{$user->id}"
            );

        $response
            ->assertForbidden();
    }

    public function test_admin_can_update_user_role(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->putJson(
                "/api/v1/admin/users/{$user->id}/role",
                [
                    'role' => 'customer',
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User role updated successfully.',
            ])
            ->assertJsonPath(
                'data.roles.0.slug',
                'customer'
            );
    }

    public function test_customer_cannot_update_user_role(): void
    {
        $customer = User::factory()->create();
        $user = User::factory()->create();

        $customerRole = Role::query()
            ->where('slug', 'customer')
            ->firstOrFail();

        $customer->roles()->attach($customerRole);

        $response = $this
            ->actingAs($customer, 'sanctum')
            ->putJson(
                "/api/v1/admin/users/{$user->id}/role",
                [
                    'role' => 'customer',
                ]
            );

        $response
            ->assertForbidden();
    }

    public function test_admin_user_role_validation_works(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->putJson(
                "/api/v1/admin/users/{$user->id}/role",
                [
                    'role' => 'not-a-real-role',
                ]
            );

        $response
            ->assertUnprocessable();
    }

    public function test_admin_cannot_update_unknown_user_role(): void
    {
        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->putJson(
                '/api/v1/admin/users/999999/role',
                [
                    'role' => 'customer',
                ]
            );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'User not found.',
            ]);
    }

    public function test_unauthenticated_user_cannot_update_user_role(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson(
            "/api/v1/admin/users/{$user->id}/role",
            [
                'role' => 'customer',
            ]
        );

        $response
            ->assertUnauthorized();
    }
}
