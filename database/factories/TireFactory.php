<?php

namespace Database\Factories;

use App\Models\Tire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tire>
 */
class TireFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['Michelin', 'Bridgestone', 'Goodyear', 'Continental', 'Pirelli'];
        $models = ['Pilot Sport', 'Potenza', 'Eagle', 'PremiumContact', 'P Zero'];
        $types = ['summer', 'winter'];

        return [
            'brand' => $this->faker->randomElement($brands),
            'model' => $this->faker->randomElement($models),
            'type' => $this->faker->randomElement($types),
            'stock' => $this->faker->numberBetween(0, 50),
        ];
    }
} 