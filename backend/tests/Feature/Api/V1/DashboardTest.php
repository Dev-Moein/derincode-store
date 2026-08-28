<?php

namespace Tests\Feature\Api\V1;

use App\Enums\PaymentStatus;
use App\Models\Download;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authenticated user can view dashboard.
     */
    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Dashboard retrieved successfully.',
            ])
            ->assertJsonPath(
                'data.user.id',
                $user->id
            )
            ->assertJsonPath(
                'data.stats.project_requests',
                0
            )
            ->assertJsonPath(
                'data.stats.purchased_projects',
                0
            )
            ->assertJsonPath(
                'data.stats.downloads',
                0
            );
    }

    /**
     * Unauthenticated user cannot view dashboard.
     */
    public function test_unauthenticated_user_cannot_view_dashboard(): void
    {
        $response = $this->getJson('/api/v1/dashboard');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Dashboard returns user's project requests.
     */
    public function test_dashboard_returns_only_authenticated_users_project_requests(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        ProjectRequest::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        ProjectRequest::factory()
            ->count(3)
            ->create([
                'user_id' => $otherUser->id,
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.stats.project_requests',
                2
            )
            ->assertJsonCount(
                2,
                'data.project_requests'
            );
    }

    /**
     * Dashboard returns successfully purchased projects.
     */
    public function test_dashboard_returns_successfully_purchased_projects(): void
    {
        $user = User::factory()->create();

        $successfulProject = Project::factory()->create();

        $failedProject = Project::factory()->create();

        Payment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $successfulProject->id,
            'status' => PaymentStatus::SUCCESSFUL,
        ]);

        Payment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $failedProject->id,
            'status' => PaymentStatus::FAILED,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.stats.purchased_projects',
                1
            )
            ->assertJsonCount(
                1,
                'data.purchased_projects'
            )
            ->assertJsonPath(
                'data.purchased_projects.0.project_id',
                $successfulProject->id
            );
    }

    /**
     * Dashboard counts unique purchased projects.
     */
    public function test_dashboard_counts_unique_purchased_projects(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create();

        Payment::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'status' => PaymentStatus::SUCCESSFUL,
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.stats.purchased_projects',
                1
            );
    }

    /**
     * Dashboard returns user's downloads.
     */
    public function test_dashboard_returns_only_authenticated_users_downloads(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        Download::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        Download::factory()
            ->count(3)
            ->create([
                'user_id' => $otherUser->id,
            ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.stats.downloads',
                2
            )
            ->assertJsonCount(
                2,
                'data.recent_downloads'
            );
    }

    /**
     * Dashboard contains the expected structure.
     */
    public function test_dashboard_returns_expected_structure(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/dashboard');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user',
                    'stats' => [
                        'project_requests',
                        'purchased_projects',
                        'downloads',
                    ],
                    'project_requests',
                    'purchased_projects',
                    'recent_downloads',
                ],
            ]);
    }
}

