<?php

namespace Tests\Unit;

use App\Models\Food;
use App\Models\FoodNutrient;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeNutrientCalculationTest extends TestCase
{
    use RefreshDatabase;

    private function createRecipeWithIngredients(): Recipe
    {
        $recipe = Recipe::factory()->create([
            'name' => 'Tartă de morcov',
            'yield_unit' => 'g',
        ]);

        // Morcovi 300g - 41 kcal/100g
        $morcovi = Food::factory()->create(['name' => 'Morcovi']);
        FoodNutrient::factory()->create([
            'food_id' => $morcovi->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 41,
            'protein_g' => 0.9,
            'fat_g' => 0.2,
            'carb_g' => 10,
            'fiber_g' => 2.8,
            'sodium_mg' => 69,
            'potassium_mg' => 320,
            'phosphorus_mg' => 35,
        ]);
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $morcovi->id,
            'qty' => 300,
            'unit' => 'g',
        ]);

        // Făină 200g - 364 kcal/100g
        $faina = Food::factory()->create(['name' => 'Făină']);
        FoodNutrient::factory()->create([
            'food_id' => $faina->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 364,
            'protein_g' => 10,
            'fat_g' => 1,
            'carb_g' => 76,
            'fiber_g' => 2.7,
            'sodium_mg' => 2,
            'potassium_mg' => 107,
            'phosphorus_mg' => 108,
        ]);
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $faina->id,
            'qty' => 200,
            'unit' => 'g',
        ]);

        // Zahăr 100g - 387 kcal/100g
        $zahar = Food::factory()->create(['name' => 'Zahăr']);
        FoodNutrient::factory()->create([
            'food_id' => $zahar->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 387,
            'protein_g' => 0,
            'fat_g' => 0,
            'carb_g' => 100,
            'fiber_g' => 0,
            'sodium_mg' => 1,
            'potassium_mg' => 2,
            'phosphorus_mg' => 0,
        ]);
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $zahar->id,
            'qty' => 100,
            'unit' => 'g',
        ]);

        $recipe->refresh();

        return $recipe;
    }

    public function test_recipe_calculates_total_nutrients_from_all_ingredients(): void
    {
        $recipe = $this->createRecipeWithIngredients();

        // Calcul manual:
        // Morcovi 300g: kcal=123, protein=2.7, fat=0.6, carb=30
        // Făină 200g: kcal=728, protein=20, fat=2, carb=152
        // Zahăr 100g: kcal=387, protein=0, fat=0, carb=100
        // TOTAL: kcal=1238, protein=22.7, fat=2.6, carb=282

        $nutrients = $recipe->calculateTotalNutrients();

        $this->assertEqualsWithDelta(1238, $nutrients['kcal'], 0.1);
        $this->assertEqualsWithDelta(22.7, $nutrients['protein_g'], 0.1);
        $this->assertEqualsWithDelta(2.6, $nutrients['fat_g'], 0.1);
        $this->assertEqualsWithDelta(282, $nutrients['carb_g'], 0.1);
    }

    public function test_recipe_calculates_nutrients_for_specific_portion(): void
    {
        $recipe = $this->createRecipeWithIngredients();
        // yield_qty = 600 (300 + 200 + 100)

        // Calculăm pentru 150g (1/4 din rețetă)
        $nutrients = $recipe->calculateNutrientsForPortion(150);

        // TOTAL rețetă: kcal=1238
        // 150g = 150/600 = 0.25 din total
        // kcal = 1238 * 0.25 = 309.5
        $this->assertEqualsWithDelta(309.5, $nutrients['kcal'], 0.1);
    }

    public function test_recipe_nutrients_per_100_returns_correct_values(): void
    {
        $recipe = $this->createRecipeWithIngredients();
        // yield_qty = 600g

        $nutrientsPer100 = $recipe->nutrients_per_100;

        // TOTAL rețetă: kcal=1238 pentru 600g
        // Per 100g: 1238 * (100/600) = 206.33
        $this->assertEqualsWithDelta(206.33, $nutrientsPer100['kcal'], 0.1);
    }

    public function test_recipe_with_no_items_returns_zero_nutrients(): void
    {
        $recipe = Recipe::factory()->create([
            'yield_qty' => 100,
        ]);

        $nutrients = $recipe->calculateTotalNutrients();

        $this->assertEquals(0, $nutrients['kcal']);
        $this->assertEquals(0, $nutrients['protein_g']);
        $this->assertEquals(0, $nutrients['fat_g']);
        $this->assertEquals(0, $nutrients['carb_g']);
    }

    public function test_recipe_item_calculates_nutrients_correctly(): void
    {
        $food = Food::factory()->create(['name' => 'Test Food']);
        FoodNutrient::factory()->create([
            'food_id' => $food->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 100,
            'protein_g' => 10,
            'fat_g' => 5,
            'carb_g' => 15,
            'fiber_g' => 2,
            'sodium_mg' => 50,
            'potassium_mg' => 200,
            'phosphorus_mg' => 100,
        ]);

        $recipe = Recipe::factory()->create();
        $recipeItem = RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $food->id,
            'qty' => 250, // 250g
            'unit' => 'g',
        ]);

        $nutrients = $recipeItem->calculateNutrients();

        // 250g = 2.5x 100g
        $this->assertEquals(250, $nutrients['kcal']);
        $this->assertEquals(25, $nutrients['protein_g']);
        $this->assertEquals(12.5, $nutrients['fat_g']);
        $this->assertEquals(37.5, $nutrients['carb_g']);
        $this->assertEquals(5, $nutrients['fiber_g']);
        $this->assertEquals(125, $nutrients['sodium_mg']);
        $this->assertEquals(500, $nutrients['potassium_mg']);
        $this->assertEquals(250, $nutrients['phosphorus_mg']);
    }

    public function test_proportional_calculation_when_serving_partial_recipe(): void
    {
        // Scenariul real: Tartă de morcov 600g, servești 150g
        $recipe = $this->createRecipeWithIngredients();
        // Total kcal = 1238 pentru 600g

        // Servești 150g (25% din rețetă)
        $portion = $recipe->calculateNutrientsForPortion(150);

        // 25% din 1238 = 309.5
        $this->assertEqualsWithDelta(309.5, $portion['kcal'], 0.5);

        // Servești 50g (8.33% din rețetă)
        $smallPortion = $recipe->calculateNutrientsForPortion(50);

        // 8.33% din 1238 = ~103.17
        $this->assertEqualsWithDelta(103.17, $smallPortion['kcal'], 0.5);
    }

    public function test_recipe_minerals_are_calculated_correctly(): void
    {
        $recipe = $this->createRecipeWithIngredients();

        // Calcul manual minerale:
        // Morcovi 300g: sodium=207, potassium=960, phosphorus=105
        // Făină 200g: sodium=4, potassium=214, phosphorus=216
        // Zahăr 100g: sodium=1, potassium=2, phosphorus=0
        // TOTAL: sodium=212, potassium=1176, phosphorus=321

        $nutrients = $recipe->calculateTotalNutrients();

        $this->assertEqualsWithDelta(212, $nutrients['sodium_mg'], 1);
        $this->assertEqualsWithDelta(1176, $nutrients['potassium_mg'], 1);
        $this->assertEqualsWithDelta(321, $nutrients['phosphorus_mg'], 1);
    }
}