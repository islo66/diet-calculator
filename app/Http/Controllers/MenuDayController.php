<?php

namespace App\Http\Controllers;

use App\Models\MenuDay;
use App\Models\MenuMeal;
use App\Models\MenuPlan;
use Illuminate\Http\Request;

class MenuDayController extends Controller
{
    public function create(MenuPlan $menuPlan)
    {
        return view('menu-days.create', compact('menuPlan'));
    }

    public function store(Request $request, MenuPlan $menuPlan)
    {
        $data = $this->validated($request);
        $data['menu_plan_id'] = $menuPlan->id;

        $menuDay = MenuDay::create($data);

        if ($request->boolean('create_default_meals')) {
            foreach (MenuMeal::mealTypes() as $index => $mealName) {
                MenuMeal::create([
                    'menu_day_id' => $menuDay->id,
                    'name' => $mealName,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('menu-plans.show', $menuPlan)
            ->with('success', 'Ziua a fost adaugata.');
    }

    public function edit(MenuDay $menuDay)
    {
        $menuDay->load('menuPlan');

        return view('menu-days.edit', compact('menuDay'));
    }

    public function update(Request $request, MenuDay $menuDay)
    {
        $data = $this->validated($request);

        $menuDay->update($data);

        return redirect()
            ->route('menu-plans.show', $menuDay->menu_plan_id)
            ->with('success', 'Ziua a fost actualizata.');
    }

    public function destroy(MenuDay $menuDay)
    {
        $menuPlanId = $menuDay->menu_plan_id;
        $menuDay->delete();

        return redirect()
            ->route('menu-plans.show', $menuPlanId)
            ->with('success', 'Ziua a fost stearsa.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}