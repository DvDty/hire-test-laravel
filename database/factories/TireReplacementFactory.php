<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\MaintenanceRequest;
use App\Models\Tire;
use App\Models\TireReplacement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TireReplacement>
 */
class TireReplacementFactory extends Factory
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
            'tire_id' => Tire::factory(),
            'position' => $this->faker->randomElement(['front_left', 'front_right', 'rear_left', 'rear_right']),
            'replaced_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'maintenance_request_id' => MaintenanceRequest::factory(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the tire replacement is for the front left position.
     */
    public function frontLeft(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'front_left',
        ]);
    }

    /**
     * Indicate that the tire replacement is for the front right position.
     */
    public function frontRight(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'front_right',
        ]);
    }

    /**
     * Indicate that the tire replacement is for the rear left position.
     */
    public function rearLeft(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'rear_left',
        ]);
    }

    /**
     * Indicate that the tire replacement is for the rear right position.
     */
    public function rearRight(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'rear_right',
        ]);
    }
}