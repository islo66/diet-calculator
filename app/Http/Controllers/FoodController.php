<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\MealItem;
use App\Models\FoodNutrient;
use App\Models\RecipeItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function duplicates()
    {
        $groupsRaw = Food::query()
            ->selectRaw('LOWER(TRIM(name)) as normalized_name')
            ->selectRaw('COALESCE(category_id, 0) as normalized_category_id')
            ->selectRaw('COUNT(*) as total')
            ->groupByRaw('LOWER(TRIM(name)), COALESCE(category_id, 0)')
            ->havingRaw('COUNT(*) > 1')
            ->orderByRaw('LOWER(TRIM(name))')
            ->get();

        $groups = [];

        foreach ($groupsRaw as $group) {
            $normalizedCategoryId = (int) $group->normalized_category_id;

            $foodsQuery = Food::query()
                ->with('category')
                ->withCount(['nutrients', 'recipeItems', 'mealItems'])
                ->whereRaw('LOWER(TRIM(name)) = ?', [$group->normalized_name]);

            if ($normalizedCategoryId === 0) {
                $foodsQuery->whereNull('category_id');
            } else {
                $foodsQuery->where('category_id', $normalizedCategoryId);
            }

            $foods = $foodsQuery->orderBy('id')->get();

            $groups[] = [
                'label' => $foods->first()?->name ?? $group->normalized_name,
                'category' => $foods->first()?->category?->name,
                'foods' => $foods,
            ];
        }

        return view('foods.duplicates', compact('groups'));
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

    public function show(Request $request, Food $food)
    {
        $perPage = (int) $request->query('per_page', 25);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $food->load(['category']);

        $nutrients = FoodNutrient::query()
            ->where('food_id', $food->id)
            ->orderBy('basis_qty')
            ->orderBy('basis_unit')
            ->paginate($perPage)
            ->withQueryString();

        return view('foods.show', compact('food', 'nutrients', 'perPage'));
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

    public function mergeDuplicates(Request $request)
    {
        $data = $request->validate([
            'keep_food_id' => ['required', 'integer', 'exists:foods,id'],
            'food_ids' => ['required', 'array', 'min:2'],
            'food_ids.*' => ['required', 'integer', 'exists:foods,id'],
            'nutrient_conflict' => ['required', 'string', 'in:keep_target,overwrite_target'],
        ]);

        $keepFoodId = (int) $data['keep_food_id'];
        $allFoodIds = array_values(array_unique(array_map('intval', $data['food_ids'])));

        if (!in_array($keepFoodId, $allFoodIds, true)) {
            return back()->with('error', 'Alimentul pastrat trebuie sa fie din acelasi grup de duplicate.');
        }

        $foodIdsToMerge = array_values(array_filter($allFoodIds, static fn (int $id) => $id !== $keepFoodId));

        if ($foodIdsToMerge === []) {
            return back()->with('error', 'Nu exista alimente de fuzionat.');
        }

        $foods = Food::query()->whereIn('id', $allFoodIds)->get(['id', 'name', 'category_id']);
        if ($foods->count() !== count($allFoodIds)) {
            return back()->with('error', 'Unele alimente selectate nu mai exista.');
        }

        $signature = $this->duplicateSignature($foods->first()->name, $foods->first()->category_id);
        $validGroup = $foods->every(fn (Food $food) => $this->duplicateSignature($food->name, $food->category_id) === $signature);

        if (!$validGroup) {
            return back()->with('error', 'Poti fuziona doar alimente duplicate din acelasi grup (nume + categorie).');
        }

        DB::transaction(function () use ($keepFoodId, $foodIdsToMerge, $data) {
            foreach ($foodIdsToMerge as $fromFoodId) {
                RecipeItem::query()
                    ->where('food_id', $fromFoodId)
                    ->update(['food_id' => $keepFoodId]);

                MealItem::query()
                    ->where('food_id', $fromFoodId)
                    ->update(['food_id' => $keepFoodId]);

                $sourceNutrients = FoodNutrient::query()
                    ->where('food_id', $fromFoodId)
                    ->orderBy('id')
                    ->get();

                foreach ($sourceNutrients as $sourceNutrient) {
                    $targetNutrient = FoodNutrient::query()
                        ->where('food_id', $keepFoodId)
                        ->where('basis_qty', $sourceNutrient->basis_qty)
                        ->where('basis_unit', $sourceNutrient->basis_unit)
                        ->first();

                    if (!$targetNutrient) {
                        $sourceNutrient->update(['food_id' => $keepFoodId]);
                        continue;
                    }

                    if ($data['nutrient_conflict'] === 'overwrite_target') {
                        $targetNutrient->update([
                            'kcal' => $sourceNutrient->kcal,
                            'protein_g' => $sourceNutrient->protein_g,
                            'fat_g' => $sourceNutrient->fat_g,
                            'carb_g' => $sourceNutrient->carb_g,
                            'fiber_g' => $sourceNutrient->fiber_g,
                            'sodium_mg' => $sourceNutrient->sodium_mg,
                            'potassium_mg' => $sourceNutrient->potassium_mg,
                            'phosphorus_mg' => $sourceNutrient->phosphorus_mg,
                            'calcium_mg' => $sourceNutrient->calcium_mg,
                            'magnesium_mg' => $sourceNutrient->magnesium_mg,
                            'iron_mg' => $sourceNutrient->iron_mg,
                        ]);
                    }

                    $sourceNutrient->delete();
                }

                Food::query()->where('id', $fromFoodId)->delete();
            }
        });

        return redirect()
            ->route('foods.duplicates')
            ->with('success', 'Duplicatele au fost fuzionate cu succes.');
    }

    private function duplicateSignature(string $name, ?int $categoryId): string
    {
        $normalizedName = strtolower(trim($name));
        $normalizedCategoryId = $categoryId ?? 0;

        return $normalizedName . '|' . $normalizedCategoryId;
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
