<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodNutrient;
use App\Models\MealItem;
use App\Models\MealType;
use App\Models\MenuDay;
use App\Models\MenuMeal;
use App\Models\MenuPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuPlanPdfExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_render_menu_plan_pdf_view(): void
    {
        $user = User::factory()->create();
        $menuPlan = MenuPlan::factory()->create();
        $day = MenuDay::factory()->create([
            'menu_plan_id' => $menuPlan->id,
            'name' => 'Luni',
        ]);

        $mealType = MealType::factory()->create([
            'name' => 'Cina',
            'default_sort_order' => 5,
        ]);

        $meal = MenuMeal::factory()->create([
            'menu_day_id' => $day->id,
            'meal_type_id' => $mealType->id,
            'name' => 'Cina',
            'sort_order' => 5,
        ]);

        $food = Food::factory()->create(['name' => 'Peste']);
        FoodNutrient::factory()->create([
            'food_id' => $food->id,
            'basis_qty' => 100,
            'basis_unit' => 'g',
            'kcal' => 180,
            'protein_g' => 25,
            'fat_g' => 8,
            'carb_g' => 0,
            'sodium_mg' => 70,
            'potassium_mg' => 320,
            'phosphorus_mg' => 210,
        ]);

        MealItem::factory()->create([
            'menu_meal_id' => $meal->id,
            'item_type' => MealItem::TYPE_FOOD,
            'food_id' => $food->id,
            'portion_qty' => 150,
            'portion_unit' => 'g',
        ]);

        $response = $this->actingAs($user)->get(route('menu-plans.pdf', $menuPlan));

        $response->assertOk();
        $response->assertSee('Grand Total Zi');
        $response->assertSee('Luni');
        $response->assertSee('Cina');
    }
}
