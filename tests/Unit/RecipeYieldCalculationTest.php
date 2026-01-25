<?php

namespace Tests\Unit;

use App\Models\Food;
use App\Models\FoodNutrient;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeYieldCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_yield_qty_is_calculated_when_item_is_added(): void
    {
        // Arrange
        $recipe = Recipe::factory()->create([
            'name' => 'Test Recipe',
            'yield_qty' => 0,
            'yield_unit' => 'g',
        ]);

        $food1 = Food::factory()->create(['name' => 'Ingredient 1']);
        $food2 = Food::factory()->create(['name' => 'Ingredient 2']);

        // Act: Adăugăm ingrediente
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food1->id,
            'qty' => 200,
            'unit' => 'g',
        ]);

        // Assert
        $recipe->refresh();
        $this->assertEquals(200, $recipe->yield_qty);

        // Act: Adăugăm al doilea ingredient
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food2->id,
            'qty' => 150,
            'unit' => 'g',
        ]);

        // Assert
        $recipe->refresh();
        $this->assertEquals(350, $recipe->yield_qty);
    }

    public function test_recipe_yield_qty_is_updated_when_item_quantity_changes(): void
    {
        // Arrange
        $recipe = Recipe::factory()->create([
            'name' => 'Test Recipe',
            'yield_qty' => 0,
            'yield_unit' => 'g',
        ]);

        $food = Food::factory()->create(['name' => 'Ingredient']);
        $recipeItem = RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food->id,
            'qty' => 100,
            'unit' => 'g',
        ]);

        $recipe->refresh();
        $this->assertEquals(100, $recipe->yield_qty);

        // Act: Modificăm cantitatea
        $recipeItem->update(['qty' => 250]);

        // Assert
        $recipe->refresh();
        $this->assertEquals(250, $recipe->yield_qty);
    }

    public function test_recipe_yield_qty_is_updated_when_item_is_deleted(): void
    {
        // Arrange
        $recipe = Recipe::factory()->create([
            'name' => 'Test Recipe',
            'yield_qty' => 0,
            'yield_unit' => 'g',
        ]);

        $food1 = Food::factory()->create(['name' => 'Ingredient 1']);
        $food2 = Food::factory()->create(['name' => 'Ingredient 2']);

        $item1 = RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food1->id,
            'qty' => 200,
            'unit' => 'g',
        ]);

        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food2->id,
            'qty' => 100,
            'unit' => 'g',
        ]);

        $recipe->refresh();
        $this->assertEquals(300, $recipe->yield_qty);

        // Act: Ștergem primul ingredient
        $item1->delete();

        // Assert
        $recipe->refresh();
        $this->assertEquals(100, $recipe->yield_qty);
    }

    public function test_calculate_total_weight_sums_all_ingredients(): void
    {
        // Arrange
        $recipe = Recipe::factory()->create();

        $food1 = Food::factory()->create();
        $food2 = Food::factory()->create();
        $food3 = Food::factory()->create();

        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food1->id,
            'qty' => 100,
            'unit' => 'g',
        ]);

        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food2->id,
            'qty' => 200,
            'unit' => 'g',
        ]);

        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food3->id,
            'qty' => 50,
            'unit' => 'g',
        ]);

        // Act
        $totalWeight = $recipe->calculateTotalWeight();

        // Assert
        $this->assertEquals(350, $totalWeight);
    }

    public function test_recipe_with_no_items_has_zero_yield(): void
    {
        $recipe = Recipe::factory()->create([
            'yield_qty' => 0,
        ]);

        $this->assertEquals(0, $recipe->calculateTotalWeight());
        $this->assertEquals(0, $recipe->yield_qty);
    }
}