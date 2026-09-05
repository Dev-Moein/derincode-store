<?php

namespace Tests\Feature\Api\V1;

use App\Enums\ProjectStatus;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Contracts\Services\PaymentGatewayInterface;
use Mockery;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
    parent::setUp();

    $this->app->instance(
        PaymentGatewayInterface::class,
        Mockery::mock(PaymentGatewayInterface::class)
    );
}
    private function admin(): User
    {
        /** @var User $user */
        $user = User::factory()->create();

        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrator role.',
            ]
        );

        $permissions = [
            'projects.create',
            'projects.update',
            'projects.delete',
        ];

        foreach ($permissions as $slug) {
            $permission = Permission::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $slug,
                    'description' => "Permission: {$slug}",
                ]
            );

            $adminRole->permissions()->syncWithoutDetaching(
                $permission->id
            );
        }

        $user->roles()->attach($adminRole);

        return $user;
    }

    public function test_public_user_can_list_projects(): void
    {
        Project::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/projects');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_public_user_can_view_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->getJson(
            "/api/v1/projects/{$project->slug}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Project retrieved successfully.',
            ]);
    }

    public function test_public_user_gets_404_for_unknown_project(): void
    {
        $response = $this->getJson(
            '/api/v1/projects/project-does-not-exist'
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Project not found.',
            ]);
    }

    public function test_authenticated_user_can_create_project(): void
    {
        $user = $this->admin();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->postJson('/api/v1/projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
            'short_description' => 'Test short description.',
            'description' => 'Test project description.',
            'price' => 1500,
            'currency' => 'USD',
            'is_for_sale' => true,
            'is_featured' => false,
            'status' => 'draft',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('projects', [
            'title' => 'Test Project',
            'slug' => 'test-project',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_project(): void
    {
        $response = $this->postJson('/api/v1/projects', [
            'title' => 'Unauthorized Project',
            'currency' => 'USD',
            'status' => 'draft',
        ]);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_project(): void
    {
        $user = $this->admin();

        $project = Project::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->putJson(
            "/api/v1/projects/{$project->slug}",
            [
                'title' => 'Updated Project',
                'status' => 'published',
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Project updated successfully.',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project',
        ]);
    }

    public function test_authenticated_user_can_delete_project(): void
    {
        $user = $this->admin();

        $project = Project::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->deleteJson(
            "/api/v1/projects/{$project->slug}"
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Project deleted successfully.',
            ]);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_update_unknown_project_returns_404(): void
    {
        $user = $this->admin();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->putJson(
            '/api/v1/projects/unknown-project',
            [
                'title' => 'Updated Project',
            ]
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Project not found.',
            ]);
    }

    public function test_delete_unknown_project_returns_404(): void
    {
        $user = $this->admin();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->deleteJson(
            '/api/v1/projects/unknown-project'
        );

        $response
            ->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Project not found.',
            ]);
    }

    public function test_projects_can_be_filtered_by_status(): void
    {
        Project::factory()->create([
            'status' => ProjectStatus::DRAFT,
        ]);

        Project::factory()->published()->create();

        $response = $this->getJson(
            '/api/v1/projects?status=published'
        );

        $response->assertOk();
    }

    public function test_projects_can_be_filtered_by_featured(): void
    {
        Project::factory()->featured()->create();

        Project::factory()->create([
            'is_featured' => false,
        ]);

        $response = $this->getJson(
            '/api/v1/projects?is_featured=1'
        );

        $response->assertOk();
    }

    public function test_projects_can_be_filtered_by_sale_status(): void
    {
        Project::factory()->create([
            'is_for_sale' => true,
        ]);

        Project::factory()->notForSale()->create();

        $response = $this->getJson(
            '/api/v1/projects?is_for_sale=1'
        );

        $response->assertOk();
    }

    public function test_projects_can_be_searched(): void
    {
        Project::factory()->create([
            'title' => 'Laravel Portfolio Project',
        ]);

        Project::factory()->create([
            'title' => 'Another Project',
        ]);

        $response = $this->getJson(
            '/api/v1/projects?search=Laravel'
        );

        $response->assertOk();
    }
}
