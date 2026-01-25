<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\FoodNutrient;
use Illuminate\Database\Eloquent\Factories\Factory;

class FoodNutrientFactory extends Factory
{
    protected $model = FoodNutrient::class;

    public function definition(): array
    {
        return [
            'food_id' => Food::factory(),
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => $this->faker->numberBetween(10, 500),
            'protein_g' => $this->faker->randomFloat(1, 0, 30),
            'fat_g' => $this->faker->randomFloat(1, 0, 30),
            'carb_g' => $this->faker->randomFloat(1, 0, 50),
            'fiber_g' => $this->faker->randomFloat(1, 0, 10),
            'sodium_mg' => $this->faker->numberBetween(0, 500),
            'potassium_mg' => $this->faker->numberBetween(0, 500),
            'phosphorus_mg' => $this->faker->numberBetween(0, 300),
        ];
    }
}