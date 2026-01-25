<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodNutrient;
use Illuminate\Http\Request;

class FoodNutrientController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $foodId = $request->query('food_id');
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = FoodNutrient::query()->with('food')->orderByDesc('id');

        if ($q !== '') {
            $query->whereHas('food', function ($qq) use ($q) {
                $qq->where('name', 'like', '%' . $q . '%');
            });
        }

        if ($foodId !== null && $foodId !== '') {
            $query->where('food_id', (int) $foodId);
        }

        $nutrients = $query->paginate($perPage)->withQueryString();

        $foods = Food::query()->orderBy('name')->get(['id', 'name']);

        return view('nutrients.index', compact('nutrients', 'foods', 'q', 'foodId', 'perPage'));
    }

    public function create(Request $request)
    {
        $foods = Food::query()->orderBy('name')->get(['id', 'name']);
        $foodId = $request->query('food_id');

        return view('nutrients.create', compact('foods', 'foodId'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $exists = FoodNutrient::query()
            ->where('food_id', $data['food_id'])
            ->where('basis_qty', $data['basis_qty'])
            ->where('basis_unit', $data['basis_unit'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'basis_qty' => 'Already exists for this food and basis.',
            ]);
        }

        FoodNutrient::create($data);

        return redirect()->route('nutrients.index', ['food_id' => $data['food_id']]);
    }

    public function edit(FoodNutrient $nutrient)
    {
        $foods = Food::query()->orderBy('name')->get(['id', 'name']);

        return view('nutrients.edit', compact('nutrient', 'foods'));
    }

    public function update(Request $request, FoodNutrient $nutrient)
    {
        $data = $this->validated($request);

        $exists = FoodNutrient::query()
            ->where('id', '!=', $nutrient->id)
            ->where('food_id', $data['food_id'])
            ->where('basis_qty', $data['basis_qty'])
            ->where('basis_unit', $data['basis_unit'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'basis_qty' => 'Already exists for this food and basis.',
            ]);
        }

        $nutrient->update($data);

        return redirect()->route('nutrients.index', ['food_id' => $data['food_id']]);
    }

    public function destroy(FoodNutrient $nutrient)
    {
        $foodId = $nutrient->food_id;
        $nutrient->delete();

        return redirect()->route('nutrients.index', ['food_id' => $foodId]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'basis_qty' => ['required', 'numeric', 'min:0.01'],
            'basis_unit' => ['required', 'string', 'max:10'],

            'kcal' => ['nullable', 'numeric', 'min:0'],
            'protein_g' => ['nullable', 'numeric', 'min:0'],
            'fat_g' => ['nullable', 'numeric', 'min:0'],
            'carb_g' => ['nullable', 'numeric', 'min:0'],
            'fiber_g' => ['nullable', 'numeric', 'min:0'],

            'sodium_mg' => ['nullable', 'numeric', 'min:0'],
            'potassium_mg' => ['nullable', 'numeric', 'min:0'],
            'phosphorus_mg' => ['nullable', 'numeric', 'min:0'],

            'calcium_mg' => ['nullable', 'numeric', 'min:0'],
            'magnesium_mg' => ['nullable', 'numeric', 'min:0'],
            'iron_mg' => ['nullable', 'numeric', 'min:0'],
        ]);
    }
}
