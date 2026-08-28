<?php

namespace Tests\Feature\Api\V1;

use App\Enums\PaymentStatus;
use App\Models\Download;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a project that has a downloadable file.
     */
    private function downloadableProject(): Project
    {
        return Project::factory()->create([
            'file_path' => 'projects/test-project.zip',
            'file_name' => 'test-project.zip',
        ]);
    }

    /**
     * Create a successful payment for a user and project.
     */
    private function successfulPayment(
        User $user,
        Project $project
    ): Payment {
        return Payment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => 100,
            'currency' => 'USD',
            'gateway' => 'zarinpal',
            'transaction_id' => 'TEST-'.fake()->unique()->numerify('######'),
            'status' => PaymentStatus::SUCCESSFUL,
            'paid_at' => now(),
        ]);
    }

    /**
     * Authenticated user can download a purchased project.
     */
    public function test_authenticated_user_can_download_purchased_project(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        $payment = $this->successfulPayment(
            $user,
            $project
        );

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->get(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response->assertOk();

        $this->assertDatabaseHas('downloads', [
            'user_id' => $user->id,
            'project_id' => $project->id,
            'payment_id' => $payment->id,
        ]);
    }

    /**
     * Unauthenticated user cannot download a project.
     */
    public function test_unauthenticated_user_cannot_download_project(): void
    {
        $project = Project::factory()->create();

        $response = $this->get(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response->assertUnauthorized();
    }

    /**
     * Unknown project cannot be downloaded.
     */
    public function test_unknown_project_cannot_be_downloaded(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            '/api/v1/downloads/projects/999999'
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);
    }

    /**
     * User cannot download a project without a file.
     */
    public function test_user_cannot_download_project_without_file(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create([
            'file_path' => null,
            'file_name' => null,
        ]);

        $this->successfulPayment(
            $user,
            $project
        );

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);
    }

    /**
     * User cannot download a project that has not been purchased.
     */
    public function test_user_cannot_download_unpurchased_project(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);

        $this->assertDatabaseMissing('downloads', [
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * User cannot download another user's purchased project.
     */
    public function test_user_cannot_download_another_users_purchased_project(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create();

        $anotherUser = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        $this->successfulPayment(
            $owner,
            $project
        );

        $response = $this->actingAs(
            $anotherUser,
            'sanctum'
        )->getJson(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);

        $this->assertDatabaseMissing('downloads', [
            'user_id' => $anotherUser->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Failed payment does not allow download.
     */
    public function test_failed_payment_does_not_allow_download(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        Payment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => 100,
            'currency' => 'USD',
            'gateway' => 'zarinpal',
            'status' => PaymentStatus::FAILED,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);

        $this->assertDatabaseMissing('downloads', [
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    /**
     * Cancelled payment does not allow download.
     */
    public function test_cancelled_payment_does_not_allow_download(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        Payment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => 100,
            'currency' => 'USD',
            'gateway' => 'zarinpal',
            'status' => PaymentStatus::CANCELLED,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);
    }

    /**
     * Download record contains request information.
     */
    public function test_download_record_contains_ip_and_user_agent(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        $payment = $this->successfulPayment(
            $user,
            $project
        );

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->withServerVariables([
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_USER_AGENT' => 'Laravel Download Test',
        ])->get(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response->assertOk();

        $this->assertDatabaseHas('downloads', [
            'user_id' => $user->id,
            'project_id' => $project->id,
            'payment_id' => $payment->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Laravel Download Test',
        ]);
    }

    /**
     * Each successful download creates a download record.
     */
    public function test_multiple_downloads_create_multiple_records(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        Storage::disk('local')->put(
            $project->file_path,
            'test project file content'
        );

        $this->successfulPayment(
            $user,
            $project
        );

        $this->actingAs(
            $user,
            'sanctum'
        )->get(
            "/api/v1/downloads/projects/{$project->id}"
        )->assertOk();

        $this->actingAs(
            $user,
            'sanctum'
        )->get(
            "/api/v1/downloads/projects/{$project->id}"
        )->assertOk();

        $this->assertDatabaseCount(
            'downloads',
            2
        );
    }

    /**
     * Missing physical project file cannot be downloaded.
     */
    public function test_missing_physical_project_file_cannot_be_downloaded(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        $project = $this->downloadableProject();

        $this->successfulPayment(
            $user,
            $project
        );

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/downloads/projects/{$project->id}"
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'project',
            ]);

        $this->assertDatabaseMissing('downloads', [
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);
    }

    public function test_authenticated_user_can_list_own_downloads(): void
    {
        $user = User::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
        ]);

        Download::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
                'payment_id' => $payment->id,
            ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/downloads');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data',
            ]);

        $this->assertCount(
            2,
            $response->json('data')
        );
    }

    public function test_unauthenticated_user_cannot_list_downloads(): void
    {
        $response = $this->getJson(
            '/api/v1/downloads'
        );

        $response->assertUnauthorized();
    }

    public function test_user_only_sees_own_downloads(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $project = Project::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $otherPayment = Payment::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $project->id,
        ]);

        $ownDownload = Download::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'payment_id' => $payment->id,
        ]);

        $otherDownload = Download::factory()->create([
            'user_id' => $otherUser->id,
            'project_id' => $project->id,
            'payment_id' => $otherPayment->id,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson('/api/v1/downloads');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $ownDownload->id,
            ])
            ->assertJsonMissing([
                'id' => $otherDownload->id,
            ]);
    }

    public function test_downloads_can_be_filtered_by_project(): void
    {
        $user = User::factory()->create();

        $projectOne = Project::factory()->create();

        $projectTwo = Project::factory()->create();

        $paymentOne = Payment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $projectOne->id,
        ]);

        $paymentTwo = Payment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $projectTwo->id,
        ]);

        $downloadOne = Download::factory()->create([
            'user_id' => $user->id,
            'project_id' => $projectOne->id,
            'payment_id' => $paymentOne->id,
        ]);

        $downloadTwo = Download::factory()->create([
            'user_id' => $user->id,
            'project_id' => $projectTwo->id,
            'payment_id' => $paymentTwo->id,
        ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            "/api/v1/downloads?project_id={$projectOne->id}"
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'id' => $downloadOne->id,
            ])
            ->assertJsonMissing([
                'id' => $downloadTwo->id,
            ]);
    }

    public function test_downloads_can_be_paginated(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create();

        $payment = Payment::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        Download::factory()
            ->count(3)
            ->create([
                'user_id' => $user->id,
                'project_id' => $project->id,
                'payment_id' => $payment->id,
            ]);

        $response = $this->actingAs(
            $user,
            'sanctum'
        )->getJson(
            '/api/v1/downloads?per_page=2'
        );

        $response
            ->assertOk()
            ->assertJsonCount(
                2,
                'data'
            );
    }
}
