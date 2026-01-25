<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\MealItem;
use App\Models\MenuMeal;
use App\Models\Recipe;
use Illuminate\Http\Request;

class MealItemController extends Controller
{
    public function create(MenuMeal $menuMeal)
    {
        $menuMeal->load('menuDay.menuPlan.patient');

        // Rețetele disponibile: globale + cele specifice pacientului
        $patientId = $menuMeal->menuDay->menuPlan->patient_id;

        $recipes = Recipe::query()
            ->with('items.food')
            ->where(function ($q) use ($patientId) {
                $q->whereNull('patient_id')
                    ->orWhere('patient_id', $patientId);
            })
            ->orderBy('name')
            ->get();

        $foodsQuery = Food::query();
        $foods = $foodsQuery->orderBy('name')->get();

        return view('meal-items.create', compact('menuMeal', 'recipes', 'foods'));
    }

    public function store(Request $request, MenuMeal $menuMeal)
    {
        $data = $this->validated($request);
        $data['menu_meal_id'] = $menuMeal->id;
        $data['item_type'] = MealItem::TYPE_RECIPE;
        $data['food_id'] = null; // Nu folosim alimente direct

        $maxSort = $menuMeal->items()->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        MealItem::create($data);

        return redirect()
            ->route('menu-plans.show', $menuMeal->menuDay->menu_plan_id)
            ->with('success', 'Reteta a fost adaugata in meniu.');
    }

    public function edit(MealItem $mealItem)
    {
        $mealItem->load('menuMeal.menuDay.menuPlan.patient');

        $patientId = $mealItem->menuMeal->menuDay->menuPlan->patient_id;

        $recipes = Recipe::query()
            ->with('items.food')
            ->where(function ($q) use ($patientId) {
                $q->whereNull('patient_id')
                    ->orWhere('patient_id', $patientId);
            })
            ->orderBy('name')
            ->get();

        return view('meal-items.edit', compact('mealItem', 'recipes'));
    }

    public function update(Request $request, MealItem $mealItem)
    {
        $data = $this->validated($request);

        $mealItem->update($data);

        return redirect()
            ->route('menu-plans.show', $mealItem->menuMeal->menuDay->menu_plan_id)
            ->with('success', 'Reteta a fost actualizata.');
    }

    public function destroy(MealItem $mealItem)
    {
        $menuPlanId = $mealItem->menuMeal->menuDay->menu_plan_id;
        $mealItem->delete();

        return redirect()
            ->route('menu-plans.show', $menuPlanId)
            ->with('success', 'Reteta a fost stearsa din meniu.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'recipe_id' => ['required', 'integer', 'exists:recipes,id'],
            'portion_qty' => ['required', 'numeric', 'min:1'],
            'portion_unit' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
