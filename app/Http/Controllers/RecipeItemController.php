<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Http\Request;

class RecipeItemController extends Controller
{
    public function create(Recipe $recipe)
    {
        $foods = Food::query()
            ->with('nutrient')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'default_unit']);

        return view('recipe-items.create', compact('recipe', 'foods'));
    }

    public function store(Request $request, Recipe $recipe)
    {
        $data = $this->validated($request);
        $data['recipe_id'] = $recipe->id;

        RecipeItem::create($data);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Ingredientul a fost adaugat.');
    }

    public function edit(RecipeItem $recipeItem)
    {
        $recipeItem->load('recipe');

        $foods = Food::query()
            ->with('nutrient')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'default_unit']);

        return view('recipe-items.edit', compact('recipeItem', 'foods'));
    }

    public function update(Request $request, RecipeItem $recipeItem)
    {
        $data = $this->validated($request);

        $recipeItem->update($data);

        return redirect()
            ->route('recipes.show', $recipeItem->recipe_id)
            ->with('success', 'Ingredientul a fost actualizat.');
    }

    public function destroy(RecipeItem $recipeItem)
    {
        $recipeId = $recipeItem->recipe_id;
        $recipeItem->delete();

        return redirect()
            ->route('recipes.show', $recipeId)
            ->with('success', 'Ingredientul a fost sters.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'food_id' => ['required', 'integer', 'exists:foods,id'],
            'qty' => ['required', 'numeric', 'min:0.001'],
            'unit' => ['required', 'string', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}