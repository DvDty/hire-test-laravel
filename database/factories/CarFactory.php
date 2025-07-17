<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Tire;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_id' => CarModel::factory(),
            'user_id' => User::factory(),
            'color' => $this->faker->safeColorName,
            'matricule' => $this->faker->unique()->bothify('??-###-??'),
            'front_tire_id' => Tire::factory(),
            'rear_tire_id' => Tire::factory(),
        ];
    }
}
