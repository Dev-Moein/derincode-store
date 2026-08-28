<?php

namespace Tests\Feature\Api\V1;

use App\Enums\ProjectRequestStatus;
use App\Models\Permission;
use App\Models\ProjectRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a customer user with project-request permissions.
     */
    private function customer(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        $role = Role::firstOrCreate(
            [
                'slug' => 'customer',
            ],
            [
                'name' => 'Customer',
                'description' => 'Customer role',
            ]
        );

        $viewPermission = Permission::firstOrCreate(
            [
                'slug' => 'project-requests.view',
            ],
            [
                'name' => 'View Own Project Requests',
                'description' => 'View own project requests',
            ]
        );

        $createPermission = Permission::firstOrCreate(
            [
                'slug' => 'project-requests.create',
            ],
            [
                'name' => 'Create Project Requests',
                'description' => 'Create project requests',
            ]
        );

        $role->permissions()->syncWithoutDetaching([
            $viewPermission->id,
            $createPermission->id,
        ]);

        $user->roles()->syncWithoutDetaching([
            $role->id,
        ]);

        return $user;
    }

    /**
     * Create an admin user with project-request permissions.
     */
    private function admin(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        $role = Role::firstOrCreate(
            [
                'slug' => 'admin',
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrator role',
            ]
        );

        $viewPermission = Permission::firstOrCreate(
            [
                'slug' => 'project-requests.view',
            ],
            [
                'name' => 'View Own Project Requests',
                'description' => 'View project requests',
            ]
        );

        $createPermission = Permission::firstOrCreate(
            [
                'slug' => 'project-requests.create',
            ],
            [
                'name' => 'Create Project Requests',
                'description' => 'Create project requests',
            ]
        );

        $managePermission = Permission::firstOrCreate(
            [
                'slug' => 'project-requests.manage',
            ],
            [
                'name' => 'Manage Project Requests',
                'description' => 'Manage project requests',
            ]
        );

        $role->permissions()->syncWithoutDetaching([
            $viewPermission->id,
            $createPermission->id,
            $managePermission->id,
        ]);

        $user->roles()->syncWithoutDetaching([
            $role->id,
        ]);

        return $user;
    }

    public function test_authenticated_user_can_list_own_project_requests(): void
    {
        $user = $this->customer();

        ProjectRequest::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        ProjectRequest::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/project-requests');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_unauthenticated_user_cannot_list_project_requests(): void
    {
        $response = $this->getJson(
            '/api/v1/project-requests'
        );

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_project_request(): void
    {
        $user = $this->customer();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/project-requests', [
            'title' => 'Need a portfolio website',
            'description' => 'I need a modern portfolio website.',
            'budget' => 2500,
            'currency' => 'USD',
        ]);

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Project request submitted successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('project_requests', [
            'user_id' => $user->id,
            'title' => 'Need a portfolio website',
            'currency' => 'USD',
            'status' => ProjectRequestStatus::PENDING->value,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_project_request(): void
    {
        $response = $this->postJson(
            '/api/v1/project-requests',
            [
                'title' => 'Unauthorized request',
                'description' => 'This request should not be created.',
                'budget' => 1000,
                'currency' => 'USD',
            ]
        );

        $response->assertUnauthorized();
    }

    public function test_project_request_validation_works(): void
    {
        $user = $this->customer();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/project-requests', [
            'title' => '',
            'description' => '',
            'currency' => 'US',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'title',
                    'description',
                    'currency',
                ],
            ]);
    }

    public function test_user_only_sees_own_project_requests(): void
    {
        $user = $this->customer();

        ProjectRequest::factory()->create([
            'user_id' => $user->id,
            'title' => 'My Request',
        ]);

        ProjectRequest::factory()->create([
            'title' => 'Other User Request',
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/project-requests');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'My Request',
            ])
            ->assertJsonMissing([
                'title' => 'Other User Request',
            ]);
    }

    public function test_customer_cannot_access_admin_project_requests(): void
    {
        $user = $this->customer();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/project-requests/admin');

        $response
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'You do not have permission to perform this action.',
            ]);
    }

    public function test_admin_can_list_all_project_requests(): void
    {
        $admin = $this->admin();

        ProjectRequest::factory()
            ->count(3)
            ->create();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->getJson('/api/v1/project-requests/admin');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_admin_can_view_project_request(): void
    {
        $admin = $this->admin();

        $projectRequest = ProjectRequest::factory()->create();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->getJson(
            "/api/v1/project-requests/admin/{$projectRequest->id}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Project request retrieved successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);
    }

    public function test_admin_gets_404_for_unknown_project_request(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->getJson(
            '/api/v1/project-requests/admin/999999'
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Project request not found.',
            ]);
    }

    public function test_customer_cannot_view_admin_project_request(): void
    {
        $user = $this->customer();

        $projectRequest = ProjectRequest::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/project-requests/admin/{$projectRequest->id}"
        );

        $response
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'You do not have permission to perform this action.',
            ]);
    }

    public function test_admin_can_update_project_request_status(): void
    {
        $admin = $this->admin();

        $projectRequest = ProjectRequest::factory()
            ->pending()
            ->create();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->putJson(
            "/api/v1/project-requests/admin/{$projectRequest->id}/status",
            [
                'status' => ProjectRequestStatus::ACCEPTED->value,
                'admin_note' => 'Request approved.',
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Project request status updated successfully.',
            ]);

        $this->assertDatabaseHas('project_requests', [
            'id' => $projectRequest->id,
            'status' => ProjectRequestStatus::ACCEPTED->value,
        ]);
    }

    public function test_admin_can_reject_project_request(): void
    {
        $admin = $this->admin();

        $projectRequest = ProjectRequest::factory()
            ->pending()
            ->create();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->putJson(
            "/api/v1/project-requests/admin/{$projectRequest->id}/status",
            [
                'status' => ProjectRequestStatus::REJECTED->value,
                'admin_note' => 'Budget is not suitable.',
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Project request status updated successfully.',
            ]);

        $this->assertDatabaseHas('project_requests', [
            'id' => $projectRequest->id,
            'status' => ProjectRequestStatus::REJECTED->value,
        ]);
    }

    public function test_customer_cannot_update_project_request_status(): void
    {
        $user = $this->customer();

        $projectRequest = ProjectRequest::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->putJson(
            "/api/v1/project-requests/admin/{$projectRequest->id}/status",
            [
                'status' => ProjectRequestStatus::ACCEPTED->value,
            ]
        );

        $response
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'You do not have permission to perform this action.',
            ]);
    }

    public function test_admin_cannot_update_unknown_project_request(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->putJson(
            '/api/v1/project-requests/admin/999999/status',
            [
                'status' => ProjectRequestStatus::ACCEPTED->value,
            ]
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Project request not found.',
            ]);
    }

    public function test_project_request_status_validation_works(): void
    {
        $admin = $this->admin();

        $projectRequest = ProjectRequest::factory()->create();

        $response = $this->actingAs(
            $admin,
            'sanctum'
        )->putJson(
            "/api/v1/project-requests/admin/{$projectRequest->id}/status",
            [
                'status' => 'invalid-status',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonStructure([
                'errors' => [
                    'status',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_admin_project_requests(): void
    {
        $response = $this->getJson(
            '/api/v1/project-requests/admin'
        );

        $response->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_update_project_request_status(): void
    {
        $response = $this->putJson(
            '/api/v1/project-requests/admin/1/status',
            [
                'status' => ProjectRequestStatus::ACCEPTED->value,
            ]
        );

        $response->assertUnauthorized();
    }
}
