<?php

namespace Tests\Feature;

use App\Models\MealType;
use App\Models\MenuMeal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_meal_types_index(): void
    {
        MealType::factory()->create(['name' => 'Test Meal']);

        $response = $this->actingAs($this->user)->get(route('meal-types.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Meal');
        $response->assertSee('Tipuri de Mese');
    }

    public function test_can_create_meal_type(): void
    {
        $response = $this->actingAs($this->user)->post(route('meal-types.store'), [
            'name' => 'Gustare Nouă',
            'default_sort_order' => 6,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('meal-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('meal_types', [
            'name' => 'Gustare Nouă',
            'default_sort_order' => 6,
            'is_active' => true,
            'is_default' => false,
        ]);
    }

    public function test_can_update_meal_type(): void
    {
        $mealType = MealType::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($this->user)->put(route('meal-types.update', $mealType), [
            'name' => 'Updated Name',
            'default_sort_order' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('meal-types.index'));
        $response->assertSessionHas('success');

        $mealType->refresh();
        $this->assertEquals('Updated Name', $mealType->name);
    }

    public function test_can_delete_custom_meal_type(): void
    {
        $mealType = MealType::factory()->create([
            'name' => 'Custom Meal',
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->user)->delete(route('meal-types.destroy', $mealType));

        $response->assertRedirect(route('meal-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('meal_types', ['id' => $mealType->id]);
    }

    public function test_cannot_delete_default_meal_type(): void
    {
        $mealType = MealType::factory()->default()->create([
            'name' => 'Default Meal',
        ]);

        $response = $this->actingAs($this->user)->delete(route('meal-types.destroy', $mealType));

        $response->assertRedirect(route('meal-types.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('meal_types', ['id' => $mealType->id]);
    }

    public function test_cannot_delete_meal_type_with_associated_meals(): void
    {
        $mealType = MealType::factory()->create(['name' => 'Used Meal']);
        MenuMeal::factory()->create(['meal_type_id' => $mealType->id]);

        $response = $this->actingAs($this->user)->delete(route('meal-types.destroy', $mealType));

        $response->assertRedirect(route('meal-types.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('meal_types', ['id' => $mealType->id]);
    }

    public function test_validation_requires_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('meal-types.store'), [
            'name' => '',
            'default_sort_order' => 1,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_meal_type_scopes_work_correctly(): void
    {
        MealType::factory()->create(['is_active' => true, 'is_default' => true, 'default_sort_order' => 2]);
        MealType::factory()->create(['is_active' => true, 'is_default' => false, 'default_sort_order' => 1]);
        MealType::factory()->create(['is_active' => false, 'is_default' => false, 'default_sort_order' => 3]);

        $this->assertCount(2, MealType::active()->get());
        $this->assertCount(1, MealType::default()->get());
        $this->assertEquals(1, MealType::ordered()->first()->default_sort_order);
    }
}
