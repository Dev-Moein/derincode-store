<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'currency' => 'USD',
            'gateway' => 'zarinpal',
            'transaction_id' => null,
            'status' => PaymentStatus::PENDING,
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => PaymentStatus::PENDING,
            'transaction_id' => null,
            'paid_at' => null,
        ]);
    }

    public function successful(): static
    {
        return $this->state(fn (): array => [
            'status' => PaymentStatus::SUCCESSFUL,
            'transaction_id' => 'TEST-'.fake()->unique()->numerify('########'),
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'status' => PaymentStatus::FAILED,
            'transaction_id' => null,
            'paid_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => PaymentStatus::CANCELLED,
            'transaction_id' => null,
            'paid_at' => null,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (): array => [
            'status' => PaymentStatus::REFUNDED,
            'paid_at' => now(),
        ]);
    }
}
