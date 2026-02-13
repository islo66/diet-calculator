<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodNutrient;
use App\Models\MealItem;
use App\Models\RecipeItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodDuplicateMergeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_duplicate_foods_page(): void
    {
        Food::factory()->create(['name' => 'Lapte']);
        Food::factory()->create(['name' => 'Lapte']);

        $response = $this->actingAs($this->user)->get(route('foods.duplicates'));

        $response->assertOk();
        $response->assertSee('Merge duplicate alimente');
        $response->assertSee('Lapte');
    }

    public function test_merge_duplicates_keeps_target_nutrient_values(): void
    {
        $keep = Food::factory()->create(['name' => 'Paine']);
        $duplicate = Food::factory()->create(['name' => 'Paine']);

        FoodNutrient::factory()->create([
            'food_id' => $keep->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 100,
        ]);

        FoodNutrient::factory()->create([
            'food_id' => $duplicate->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 220,
        ]);

        FoodNutrient::factory()->create([
            'food_id' => $duplicate->id,
            'basis_qty' => 50,
            'basis_unit' => 'g',
            'kcal' => 111,
        ]);

        RecipeItem::factory()->create(['food_id' => $duplicate->id]);
        MealItem::factory()->create(['food_id' => $duplicate->id, 'item_type' => MealItem::TYPE_FOOD]);

        $response = $this->actingAs($this->user)->post(route('foods.duplicates.merge'), [
            'keep_food_id' => $keep->id,
            'food_ids' => [$keep->id, $duplicate->id],
            'nutrient_conflict' => 'keep_target',
        ]);

        $response->assertRedirect(route('foods.duplicates'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('foods', ['id' => $duplicate->id]);
        $this->assertDatabaseHas('recipe_items', ['food_id' => $keep->id]);
        $this->assertDatabaseHas('meal_items', ['food_id' => $keep->id]);

        $this->assertDatabaseHas('food_nutrients', [
            'food_id' => $keep->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 100,
        ]);

        $this->assertDatabaseHas('food_nutrients', [
            'food_id' => $keep->id,
            'basis_qty' => 50,
            'basis_unit' => 'g',
            'kcal' => 111,
        ]);
    }

    public function test_merge_duplicates_can_overwrite_target_nutrient_values(): void
    {
        $keep = Food::factory()->create(['name' => 'Orez']);
        $duplicate = Food::factory()->create(['name' => 'Orez']);

        FoodNutrient::factory()->create([
            'food_id' => $keep->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 100,
        ]);

        FoodNutrient::factory()->create([
            'food_id' => $duplicate->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 333,
        ]);

        $response = $this->actingAs($this->user)->post(route('foods.duplicates.merge'), [
            'keep_food_id' => $keep->id,
            'food_ids' => [$keep->id, $duplicate->id],
            'nutrient_conflict' => 'overwrite_target',
        ]);

        $response->assertRedirect(route('foods.duplicates'));
        $this->assertDatabaseMissing('foods', ['id' => $duplicate->id]);

        $this->assertDatabaseHas('food_nutrients', [
            'food_id' => $keep->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 333,
        ]);
    }
}
