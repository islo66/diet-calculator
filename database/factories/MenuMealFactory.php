<?php

namespace Database\Factories;

use App\Models\MealType;
use App\Models\MenuDay;
use App\Models\MenuMeal;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuMealFactory extends Factory
{
    protected $model = MenuMeal::class;

    public function definition(): array
    {
        return [
            'menu_day_id' => MenuDay::factory(),
            'meal_type_id' => MealType::factory(),
            'name' => $this->faker->randomElement(['Mic dejun', 'Pranz', 'Cina', 'Gustare']),
            'sort_order' => $this->faker->numberBetween(1, 4),
        ];
    }

    public function withMealType(MealType $mealType): static
    {
        return $this->state(fn() => [
            'meal_type_id' => $mealType->id,
            'name' => $mealType->name,
            'sort_order' => $mealType->default_sort_order,
        ]);
    }
}