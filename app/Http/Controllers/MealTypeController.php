<?php

namespace App\Http\Controllers;

use App\Models\MealType;
use Illuminate\Http\Request;

class MealTypeController extends Controller
{
    public function index()
    {
        $mealTypes = MealType::query()
            ->orderBy('default_sort_order')
            ->orderBy('name')
            ->paginate(25);

        return view('meal-types.index', compact('mealTypes'));
    }

    public function create()
    {
        return view('meal-types.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        MealType::create($data);

        return redirect()
            ->route('meal-types.index')
            ->with('success', 'Tipul de masa a fost creat.');
    }

    public function edit(MealType $mealType)
    {
        return view('meal-types.edit', compact('mealType'));
    }

    public function update(Request $request, MealType $mealType)
    {
        $data = $this->validated($request);

        $mealType->update($data);

        return redirect()
            ->route('meal-types.index')
            ->with('success', 'Tipul de masa a fost actualizat.');
    }

    public function destroy(MealType $mealType)
    {
        // Nu permite ștergerea tipurilor default
        if ($mealType->is_default) {
            return redirect()
                ->route('meal-types.index')
                ->with('error', 'Nu poti sterge tipurile de masa default. Le poti dezactiva.');
        }

        // Verifică dacă sunt mese asociate
        if ($mealType->menuMeals()->count() > 0) {
            return redirect()
                ->route('meal-types.index')
                ->with('error', 'Nu poti sterge acest tip de masa deoarece exista mese asociate.');
        }

        $mealType->delete();

        return redirect()
            ->route('meal-types.index')
            ->with('success', 'Tipul de masa a fost sters.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'default_sort_order' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
        ]);
    }
}