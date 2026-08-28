<?php

namespace Database\Factories;

use App\Models\Download;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Download>
 */
class DownloadFactory extends Factory
{
    protected $model = Download::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'project_id' => Project::factory(),

            'payment_id' => Payment::factory(),

            'downloaded_at' => now(),

            'ip_address' => $this->faker->ipv4(),

            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
