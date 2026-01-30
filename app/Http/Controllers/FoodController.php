<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $categoryId = $request->query('category_id');
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $query = Food::query()->with(['category', 'nutrient']);

        if ($q !== '') {
            $query->where('name', 'like', '%' . $q . '%');
        }

        if ($categoryId !== null && $categoryId !== '') {
            $query->where('category_id', (int) $categoryId);
        }

        $foods = $query->orderBy('name')->paginate($perPage)->withQueryString();

        $categories = FoodCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('foods.index', compact('foods', 'categories', 'q', 'categoryId', 'perPage'));
    }

    public function create()
    {
        $categories = FoodCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('foods.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $food = Food::create($data);

        return redirect()
            ->route('foods.show', $food)
            ->with('success', 'Alimentul a fost creat. Adauga valorile nutritionale.');
    }

    public function show(Food $food)
    {
        $food->load(['category', 'nutrients']);

        return view('foods.show', compact('food'));
    }

    public function edit(Food $food)
    {
        $categories = FoodCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('foods.edit', compact('food', 'categories'));
    }

    public function update(Request $request, Food $food)
    {
        $data = $this->validated($request);

        $food->update($data);

        return redirect()
            ->route('foods.show', $food)
            ->with('success', 'Alimentul a fost actualizat.');
    }

    public function destroy(Food $food)
    {
        if ($food->recipeItems()->count() > 0) {
            return redirect()
                ->route('foods.index')
                ->with('error', 'Nu poti sterge acest aliment deoarece este folosit in retete.');
        }

        $food->delete();

        return redirect()
            ->route('foods.index')
            ->with('success', 'Alimentul a fost sters.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'category_id' => ['nullable', 'integer', 'exists:food_categories,id'],
            'default_unit' => ['required', 'string', 'in:g,ml,pcs'],
            'density_g_per_ml' => ['nullable', 'numeric', 'min:0.001'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
