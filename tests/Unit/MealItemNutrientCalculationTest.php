<?php

namespace Tests\Unit;

use App\Models\Food;
use App\Models\FoodNutrient;
use App\Models\MealItem;
use App\Models\MenuMeal;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealItemNutrientCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_meal_item_calculates_nutrients_for_food_type(): void
    {
        // Arrange: Creăm un aliment cu nutrienți cunoscuți
        $food = Food::factory()->create(['name' => 'Lapte']);
        FoodNutrient::factory()->create([
            'food_id' => $food->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 60,
            'protein_g' => 3.2,
            'fat_g' => 3.5,
            'carb_g' => 4.8,
            'sodium_mg' => 40,
            'potassium_mg' => 150,
            'phosphorus_mg' => 90,
        ]);

        $menuMeal = MenuMeal::factory()->create();
        $mealItem = MealItem::factory()->asFood($food)->create([
            'menu_meal_id' => $menuMeal->id,
            'portion_qty' => 200, // 200g de lapte
            'portion_unit' => 'g',
        ]);

        // Act
        $nutrients = $mealItem->calculateNutrients();

        // Assert: valorile trebuie să fie duble (200g = 2x 100g)
        $this->assertEquals(120, $nutrients['kcal']);
        $this->assertEquals(6.4, $nutrients['protein_g']);
        $this->assertEquals(7.0, $nutrients['fat_g']);
        $this->assertEquals(9.6, $nutrients['carb_g']);
        $this->assertEquals(80, $nutrients['sodium_mg']);
        $this->assertEquals(300, $nutrients['potassium_mg']);
        $this->assertEquals(180, $nutrients['phosphorus_mg']);
    }

    public function test_meal_item_calculates_nutrients_for_recipe_type(): void
    {
        // Arrange: Creăm o rețetă cu ingrediente
        $recipe = Recipe::factory()->create([
            'name' => 'Piure de cartofi',
            'yield_qty' => 500, // 500g total
            'yield_unit' => 'g',
        ]);

        // Ingredient 1: Cartofi 400g
        $cartofi = Food::factory()->create(['name' => 'Cartofi']);
        FoodNutrient::factory()->create([
            'food_id' => $cartofi->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 77,
            'protein_g' => 2,
            'fat_g' => 0.1,
            'carb_g' => 17,
            'sodium_mg' => 6,
            'potassium_mg' => 421,
            'phosphorus_mg' => 57,
        ]);
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $cartofi->id,
            'qty' => 400,
            'unit' => 'g',
        ]);

        // Ingredient 2: Unt 50g
        $unt = Food::factory()->create(['name' => 'Unt']);
        FoodNutrient::factory()->create([
            'food_id' => $unt->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 717,
            'protein_g' => 0.9,
            'fat_g' => 81,
            'carb_g' => 0.1,
            'sodium_mg' => 11,
            'potassium_mg' => 24,
            'phosphorus_mg' => 24,
        ]);
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $unt->id,
            'qty' => 50,
            'unit' => 'g',
        ]);

        // Ingredient 3: Lapte 50ml
        $lapte = Food::factory()->create(['name' => 'Lapte']);
        FoodNutrient::factory()->create([
            'food_id' => $lapte->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 60,
            'protein_g' => 3.2,
            'fat_g' => 3.5,
            'carb_g' => 4.8,
            'sodium_mg' => 40,
            'potassium_mg' => 150,
            'phosphorus_mg' => 90,
        ]);
        RecipeItem::factory()->create([
            'recipe_id' => $recipe->id,
            'food_id' => $lapte->id,
            'qty' => 50,
            'unit' => 'g',
        ]);

        // Calculăm nutrienții totali ai rețetei
        // Cartofi 400g: kcal=308, protein=8, fat=0.4, carb=68
        // Unt 50g: kcal=358.5, protein=0.45, fat=40.5, carb=0.05
        // Lapte 50g: kcal=30, protein=1.6, fat=1.75, carb=2.4
        // TOTAL: kcal=696.5, protein=10.05, fat=42.65, carb=70.45

        $menuMeal = MenuMeal::factory()->create();
        $mealItem = MealItem::factory()->asRecipe($recipe)->create([
            'menu_meal_id' => $menuMeal->id,
            'portion_qty' => 250, // 250g din 500g (jumătate)
            'portion_unit' => 'g',
        ]);

        // Act
        $nutrients = $mealItem->calculateNutrients();

        // Assert: valorile trebuie să fie jumătate (250g din 500g)
        $this->assertEqualsWithDelta(348.25, $nutrients['kcal'], 0.1);
        $this->assertEqualsWithDelta(5.025, $nutrients['protein_g'], 0.01);
        $this->assertEqualsWithDelta(21.325, $nutrients['fat_g'], 0.01);
        $this->assertEqualsWithDelta(35.225, $nutrients['carb_g'], 0.01);
    }

    public function test_meal_item_returns_zeros_when_food_has_no_nutrients(): void
    {
        $food = Food::factory()->create(['name' => 'Aliment fără nutrienți']);
        // Nu creăm FoodNutrient

        $menuMeal = MenuMeal::factory()->create();
        $mealItem = MealItem::factory()->asFood($food)->create([
            'menu_meal_id' => $menuMeal->id,
            'portion_qty' => 100,
            'portion_unit' => 'g',
        ]);

        $nutrients = $mealItem->calculateNutrients();

        $this->assertEquals(0, $nutrients['kcal']);
        $this->assertEquals(0, $nutrients['protein_g']);
        $this->assertEquals(0, $nutrients['fat_g']);
        $this->assertEquals(0, $nutrients['carb_g']);
    }

    public function test_meal_item_name_returns_food_name_for_food_type(): void
    {
        $food = Food::factory()->create(['name' => 'Morcovi']);
        $menuMeal = MenuMeal::factory()->create();
        $mealItem = MealItem::factory()->asFood($food)->create([
            'menu_meal_id' => $menuMeal->id,
        ]);

        $this->assertEquals('Morcovi', $mealItem->name);
    }

    public function test_meal_item_name_returns_recipe_name_for_recipe_type(): void
    {
        $recipe = Recipe::factory()->create(['name' => 'Supă de legume']);
        $menuMeal = MenuMeal::factory()->create();
        $mealItem = MealItem::factory()->asRecipe($recipe)->create([
            'menu_meal_id' => $menuMeal->id,
        ]);

        $this->assertEquals('Supă de legume', $mealItem->name);
    }

    public function test_menu_meal_calculates_total_nutrients_from_multiple_items(): void
    {
        $menuMeal = MenuMeal::factory()->create();

        // Item 1: Aliment
        $food1 = Food::factory()->create(['name' => 'Pâine']);
        FoodNutrient::factory()->create([
            'food_id' => $food1->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 250,
            'protein_g' => 8,
            'fat_g' => 1,
            'carb_g' => 50,
            'sodium_mg' => 500,
            'potassium_mg' => 100,
            'phosphorus_mg' => 80,
        ]);
        MealItem::factory()->asFood($food1)->create([
            'menu_meal_id' => $menuMeal->id,
            'portion_qty' => 100,
            'portion_unit' => 'g',
        ]);

        // Item 2: Altă mâncare
        $food2 = Food::factory()->create(['name' => 'Brânză']);
        FoodNutrient::factory()->create([
            'food_id' => $food2->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 350,
            'protein_g' => 25,
            'fat_g' => 28,
            'carb_g' => 1,
            'sodium_mg' => 600,
            'potassium_mg' => 80,
            'phosphorus_mg' => 500,
        ]);
        MealItem::factory()->asFood($food2)->create([
            'menu_meal_id' => $menuMeal->id,
            'portion_qty' => 50, // 50g
            'portion_unit' => 'g',
        ]);

        // Refresh pentru a încărca relațiile
        $menuMeal->refresh();

        // Act
        $nutrients = $menuMeal->calculateNutrients();

        // Assert
        // Pâine 100g: kcal=250, protein=8
        // Brânză 50g: kcal=175, protein=12.5
        // Total: kcal=425, protein=20.5
        $this->assertEquals(425, $nutrients['kcal']);
        $this->assertEquals(20.5, $nutrients['protein_g']);
    }
}