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

        $foods = Food::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('meal-items.create', compact('menuMeal', 'recipes', 'foods'));
    }

    public function store(Request $request, MenuMeal $menuMeal)
    {
        $data = $this->validated($request);
        $data['menu_meal_id'] = $menuMeal->id;

        // Setează tipul și ID-urile corespunzătoare
        if ($data['item_type'] === MealItem::TYPE_FOOD) {
            $data['recipe_id'] = null;
        } else {
            $data['food_id'] = null;
        }

        $maxSort = $menuMeal->items()->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        MealItem::create($data);

        $message = $data['item_type'] === MealItem::TYPE_FOOD
            ? 'Alimentul a fost adaugat in meniu.'
            : 'Reteta a fost adaugata in meniu.';

        return redirect()
            ->route('menu-plans.show', $menuMeal->menuDay->menu_plan_id)
            ->with('success', $message);
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

        $foods = Food::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('meal-items.edit', compact('mealItem', 'recipes', 'foods'));
    }

    public function update(Request $request, MealItem $mealItem)
    {
        $data = $this->validated($request);

        // Setează tipul și ID-urile corespunzătoare
        if ($data['item_type'] === MealItem::TYPE_FOOD) {
            $data['recipe_id'] = null;
        } else {
            $data['food_id'] = null;
        }

        $mealItem->update($data);

        $message = $data['item_type'] === MealItem::TYPE_FOOD
            ? 'Alimentul a fost actualizat.'
            : 'Reteta a fost actualizata.';

        return redirect()
            ->route('menu-plans.show', $mealItem->menuMeal->menuDay->menu_plan_id)
            ->with('success', $message);
    }

    public function destroy(MealItem $mealItem)
    {
        $menuPlanId = $mealItem->menuMeal->menuDay->menu_plan_id;
        $isFood = $mealItem->item_type === MealItem::TYPE_FOOD;
        $mealItem->delete();

        $message = $isFood
            ? 'Alimentul a fost sters din meniu.'
            : 'Reteta a fost stearsa din meniu.';

        return redirect()
            ->route('menu-plans.show', $menuPlanId)
            ->with('success', $message);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'item_type' => ['required', 'string', 'in:food,recipe'],
            'food_id' => ['required_if:item_type,food', 'nullable', 'integer', 'exists:foods,id'],
            'recipe_id' => ['required_if:item_type,recipe', 'nullable', 'integer', 'exists:recipes,id'],
            'portion_qty' => ['required', 'numeric', 'min:0.001'],
            'portion_unit' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
