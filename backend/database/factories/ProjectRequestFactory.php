<?php

namespace Database\Factories;

use App\Enums\ProjectRequestStatus;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectRequest>
 */
class ProjectRequestFactory extends Factory
{
    protected $model = ProjectRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'title' => fake()->sentence(4),

            'description' => fake()->paragraph(),

            'budget' => fake()->randomFloat(2, 100, 10000),

            'currency' => 'USD',

            'status' => ProjectRequestStatus::PENDING,
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'status' => ProjectRequestStatus::PENDING,
        ]);
    }

    public function reviewing(): static
    {
        return $this->state([
            'status' => ProjectRequestStatus::REVIEWING,
        ]);
    }

    public function accepted(): static
    {
        return $this->state([
            'status' => ProjectRequestStatus::ACCEPTED,
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => ProjectRequestStatus::REJECTED,
        ]);
    }

    public function completed(): static
    {
        return $this->state([
            'status' => ProjectRequestStatus::COMPLETED,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status' => ProjectRequestStatus::CANCELLED,
        ]);
    }
}
