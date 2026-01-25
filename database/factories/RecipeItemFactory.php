<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecipeItemFactory extends Factory
{
    protected $model = RecipeItem::class;

    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::factory(),
            'food_id' => Food::factory(),
            'qty' => $this->faker->numberBetween(50, 500),
            'unit' => 'g',
        ];
    }
}