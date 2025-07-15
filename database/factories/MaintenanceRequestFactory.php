<?php

namespace Database\Factories;

use App\Enums\MaintenanceRequestStatus;
use App\Models\Car;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRequest>
 */
class MaintenanceRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(MaintenanceRequestStatus::cases()),
            'scheduled_date' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'completed_date' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'completed_date' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'completed_date' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_date' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}