<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodNutrient;
use App\Models\MealItem;
use App\Models\MenuMeal;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealItemControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_meal_item_with_food_type(): void
    {
        $menuMeal = MenuMeal::factory()->create();
        $food = Food::factory()->create(['name' => 'Lapte']);
        FoodNutrient::factory()->create(['food_id' => $food->id]);

        $response = $this->actingAs($this->user)->post(
            route('meal-items.store', $menuMeal),
            [
                'item_type' => 'food',
                'food_id' => $food->id,
                'portion_qty' => 200,
                'portion_unit' => 'ml',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meal_items', [
            'menu_meal_id' => $menuMeal->id,
            'item_type' => 'food',
            'food_id' => $food->id,
            'recipe_id' => null,
            'portion_qty' => 200,
            'portion_unit' => 'ml',
        ]);
    }

    public function test_can_create_meal_item_with_recipe_type(): void
    {
        $menuMeal = MenuMeal::factory()->create();
        $recipe = Recipe::factory()->create(['name' => 'Supă']);

        $response = $this->actingAs($this->user)->post(
            route('meal-items.store', $menuMeal),
            [
                'item_type' => 'recipe',
                'recipe_id' => $recipe->id,
                'portion_qty' => 300,
                'portion_unit' => 'g',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meal_items', [
            'menu_meal_id' => $menuMeal->id,
            'item_type' => 'recipe',
            'food_id' => null,
            'recipe_id' => $recipe->id,
            'portion_qty' => 300,
            'portion_unit' => 'g',
        ]);
    }

    public function test_validation_requires_food_id_when_type_is_food(): void
    {
        $menuMeal = MenuMeal::factory()->create();

        $response = $this->actingAs($this->user)->post(
            route('meal-items.store', $menuMeal),
            [
                'item_type' => 'food',
                'food_id' => null,
                'portion_qty' => 100,
                'portion_unit' => 'g',
            ]
        );

        $response->assertSessionHasErrors('food_id');
    }

    public function test_validation_requires_recipe_id_when_type_is_recipe(): void
    {
        $menuMeal = MenuMeal::factory()->create();

        $response = $this->actingAs($this->user)->post(
            route('meal-items.store', $menuMeal),
            [
                'item_type' => 'recipe',
                'recipe_id' => null,
                'portion_qty' => 100,
                'portion_unit' => 'g',
            ]
        );

        $response->assertSessionHasErrors('recipe_id');
    }

    public function test_can_update_meal_item_from_food_to_recipe(): void
    {
        $food = Food::factory()->create();
        $recipe = Recipe::factory()->create();
        $menuMeal = MenuMeal::factory()->create();

        $mealItem = MealItem::factory()->asFood($food)->create([
            'menu_meal_id' => $menuMeal->id,
            'portion_qty' => 100,
            'portion_unit' => 'g',
        ]);

        $response = $this->actingAs($this->user)->put(
            route('meal-items.update', $mealItem),
            [
                'item_type' => 'recipe',
                'recipe_id' => $recipe->id,
                'portion_qty' => 250,
                'portion_unit' => 'g',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $mealItem->refresh();
        $this->assertEquals('recipe', $mealItem->item_type);
        $this->assertEquals($recipe->id, $mealItem->recipe_id);
        $this->assertNull($mealItem->food_id);
        $this->assertEquals(250, $mealItem->portion_qty);
    }

    public function test_can_delete_meal_item(): void
    {
        $menuMeal = MenuMeal::factory()->create();
        $mealItem = MealItem::factory()->create([
            'menu_meal_id' => $menuMeal->id,
        ]);

        $response = $this->actingAs($this->user)->delete(
            route('meal-items.destroy', $mealItem)
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('meal_items', [
            'id' => $mealItem->id,
        ]);
    }

    public function test_create_page_shows_both_foods_and_recipes(): void
    {
        $menuMeal = MenuMeal::factory()->create();
        $food = Food::factory()->create(['name' => 'Lapte de test', 'is_active' => true]);
        $recipe = Recipe::factory()->create(['name' => 'Supa de test']);

        $response = $this->actingAs($this->user)->get(
            route('meal-items.create', $menuMeal)
        );

        $response->assertStatus(200);
        $response->assertSee('Lapte de test', false); // false = don't escape
        $response->assertSee('Supa de test', false);
        $response->assertSee('Aliment');
        $response->assertSee('Reteta / Mancare');
    }
}
