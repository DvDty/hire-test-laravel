<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\EngineType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CarModel>
 */
class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomModel' => $this->faker->word,
            'brand_id' => Brand::factory(),
            'engine_type_id' => EngineType::factory(),
        ];
    }
}
