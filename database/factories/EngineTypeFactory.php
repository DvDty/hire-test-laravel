<?php

namespace Database\Factories;

use App\Models\EngineType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EngineType>
 */
class EngineTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Petrol', 'Hybrid', 'Electric']),
            'pollution_rate' => $this->faker->numberBetween(0, 50),
            'economy_rate' => $this->faker->numberBetween(0, 50),
        ];
    }
}
