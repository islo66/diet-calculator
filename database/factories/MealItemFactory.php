<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\MealItem;
use App\Models\MenuMeal;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealItemFactory extends Factory
{
    protected $model = MealItem::class;

    public function definition(): array
    {
        return [
            'menu_meal_id' => MenuMeal::factory(),
            'item_type' => MealItem::TYPE_FOOD,
            'food_id' => Food::factory(),
            'recipe_id' => null,
            'portion_qty' => $this->faker->numberBetween(50, 300),
            'portion_unit' => 'g',
            'sort_order' => 1,
        ];
    }

    public function asFood(Food $food = null): static
    {
        return $this->state(fn() => [
            'item_type' => MealItem::TYPE_FOOD,
            'food_id' => $food?->id ?? Food::factory(),
            'recipe_id' => null,
        ]);
    }

    public function asRecipe(Recipe $recipe = null): static
    {
        return $this->state(fn() => [
            'item_type' => MealItem::TYPE_RECIPE,
            'food_id' => null,
            'recipe_id' => $recipe?->id ?? Recipe::factory(),
        ]);
    }
}