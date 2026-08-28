<?php

namespace Tests\Feature\Api\V1;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authenticated user can view their profile.
     */
    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/v1/profile');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Profile retrieved successfully.',
            ])
            ->assertJsonPath(
                'data.user.email',
                $user->email
            );
    }

    /**
     * Unauthenticated user cannot view profile.
     */
    public function test_unauthenticated_user_cannot_view_profile(): void
    {
        $response = $this->getJson('/api/v1/profile');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Authenticated user can update their profile.
     */
    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'phone' => '09123456789',
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully.',
            ])
            ->assertJsonPath(
                'data.user.name',
                'New Name'
            )
            ->assertJsonPath(
                'data.user.email',
                'new@example.com'
            );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
            'phone' => '09123456789',
        ]);
    }

    /**
     * Profile update validation works.
     */
    public function test_profile_update_validation_works(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile', [
                'name' => '',
                'email' => 'invalid-email',
            ]);

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

    /**
     * Authenticated user can change password.
     */
    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/v1/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Password changed successfully.',
            ]);
    }

    /**
     * User cannot change password with incorrect current password.
     */
    public function test_user_cannot_change_password_with_incorrect_current_password(): void
{
    $user = User::factory()->create([
        'password' => 'correct-password',
    ]);

    $response = $this
        ->actingAs($user, 'sanctum')
        ->putJson('/api/v1/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'message' => 'Current password is incorrect.',
        ])
        ->assertJsonValidationErrors([
            'current_password',
        ]);
}

    /**
     * Unauthenticated user cannot change password.
     */
    public function test_unauthenticated_user_cannot_change_password(): void
    {
        $response = $this->putJson(
            '/api/v1/profile/password',
            [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]
        );

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }
}

