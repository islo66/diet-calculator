<?php

namespace Database\Factories;

use App\Models\MealType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealTypeFactory extends Factory
{
    protected $model = MealType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Mic dejun', 'Gustare', 'Pranz', 'Cina', 'Gustare seara']),
            'default_sort_order' => $this->faker->numberBetween(1, 10),
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function default(): static
    {
        return $this->state(fn() => [
            'is_default' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
        ]);
    }
}