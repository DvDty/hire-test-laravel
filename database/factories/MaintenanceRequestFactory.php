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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(MaintenanceRequestStatus::cases()),
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'completed_date' => null,
        ];
    }
}
