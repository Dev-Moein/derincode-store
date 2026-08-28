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
            ->assertJson([
                'success' => true,
                'message' => 'Users retrieved successfully.',
            ])
            ->assertJsonCount(
                4,
                'data.users'
            );
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
                'data.users.0.email',
                'ali@example.com'
            )
            ->assertJsonCount(
                1,
                'data.users'
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
                'data.user.id',
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

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->putJson(
                "/api/v1/admin/users/{$user->id}",
                [
                    'name' => 'Updated User',
                    'email' => 'updated@example.com',
                    'phone' => '09121234567',
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User updated successfully.',
            ])
            ->assertJsonPath(
                'data.user.name',
                'Updated User'
            )
            ->assertJsonPath(
                'data.user.email',
                'updated@example.com'
            );

        $this->assertDatabaseHas(
            'users',
            [
                'id' => $user->id,
                'name' => 'Updated User',
                'email' => 'updated@example.com',
            ]
        );
    }

    public function test_customer_cannot_update_user(): void
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
                "/api/v1/admin/users/{$user->id}",
                [
                    'name' => 'Hacked User',
                    'email' => 'hacked@example.com',
                ]
            );

        $response
            ->assertForbidden();
    }

    public function test_admin_user_update_validation_works(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->putJson(
                "/api/v1/admin/users/{$user->id}",
                [
                    'name' => '',
                    'email' => 'invalid-email',
                ]
            );

        $response
            ->assertUnprocessable()
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'name',
                    'email',
                ],
            ]);
    }

    public function test_admin_cannot_update_unknown_user(): void
    {
        $response = $this
            ->actingAs($this->admin, 'sanctum')
            ->putJson(
                '/api/v1/admin/users/999999',
                [
                    'name' => 'Unknown User',
                    'email' => 'unknown@example.com',
                ]
            );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'User not found.',
            ]);
    }

    public function test_unauthenticated_user_cannot_update_user(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson(
            "/api/v1/admin/users/{$user->id}",
            [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
            ]
        );

        $response
            ->assertUnauthorized();
    }
}
