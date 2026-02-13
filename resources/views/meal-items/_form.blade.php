@php
    // Pregătim datele pentru componentele searchable
    $foodOptions = $foods->map(function($food) {
        $kcal = $food->nutrient?->kcal ?? 0;
        return [
            'id' => $food->id,
            'name' => $food->name,
            'subtitle' => $food->nutrient ? number_format($kcal, 0) . ' kcal/100' . $food->default_unit : null,
            'unit' => $food->default_unit,
        ];
    })->toArray();

    $recipeOptions = $recipes->map(function($recipe) {
        $totalNutrients = $recipe->calculateTotalNutrients();
        $per100Kcal = $recipe->yield_qty > 0 ? ($totalNutrients['kcal'] / $recipe->yield_qty) * 100 : 0;
        return [
            'id' => $recipe->id,
            'name' => $recipe->name,
            'subtitle' => number_format($per100Kcal, 0) . ' kcal/100' . $recipe->yield_unit . ', total ' . $recipe->yield_qty . $recipe->yield_unit,
            'unit' => $recipe->yield_unit,
        ];
    })->toArray();
@endphp

@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div
    x-data="{
        itemType: '{{ old('item_type', $mealItem->item_type ?? 'food') }}',
        foodOptions: {{ Js::from($foodOptions) }},
        recipeOptions: {{ Js::from($recipeOptions) }},

        setUnit(options, selectedId) {
            const option = options.find(o => o.id == selectedId);
            if (option && option.unit) {
                document.getElementById('portion_unit').value = option.unit;
            }
        }
    }"
    class="grid grid-cols-12 gap-4"
>
    {{-- Tip item --}}
    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.meal_items.type') }} *</label>
        <div class="flex gap-4">
            <label class="inline-flex items-center cursor-pointer">
                <input
                    type="radio"
                    name="item_type"
                    value="food"
                    class="form-radio text-emerald-600"
                    x-model="itemType"
                >
                <span class="ml-2">{{ __('app.foods.title') }}</span>
            </label>
            <label class="inline-flex items-center cursor-pointer">
                <input
                    type="radio"
                    name="item_type"
                    value="recipe"
                    class="form-radio text-emerald-600"
                    x-model="itemType"
                >
                <span class="ml-2">{{ __('app.recipes.recipe_or_meal') }}</span>
            </label>
        </div>
    </div>

    {{-- Selector Aliment --}}
    <div class="col-span-12" x-show="itemType === 'food'" x-cloak>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.meal_items.food') }} *</label>
        <x-searchable-select
            name="food_id"
            :options="$foodOptions"
            :value="old('food_id', $mealItem->food_id ?? '')"
            placeholder="{{ __('app.meal_items.search_food_placeholder') }}"
            @change="setUnit(foodOptions, $event.target.value)"
        />
    </div>

    {{-- Selector Reteta --}}
    <div class="col-span-12" x-show="itemType === 'recipe'" x-cloak>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.recipes.recipe_or_meal') }} *</label>
        <x-searchable-select
            name="recipe_id"
            :options="$recipeOptions"
            :value="old('recipe_id', $mealItem->recipe_id ?? '')"
            placeholder="{{ __('app.meal_items.search_recipe_placeholder') }}"
            @change="setUnit(recipeOptions, $event.target.value)"
        />
    </div>

    <div class="col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.meal_items.quantity') }} *</label>
        <input
            type="number"
            name="portion_qty"
            value="{{ old('portion_qty', $mealItem->portion_qty ?? 100) }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            step="0.001"
            min="0.001"
            required
        >
    </div>

    <div class="col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.foods.unit') }} *</label>
        <select
            name="portion_unit"
            id="portion_unit"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
            <option value="g" {{ old('portion_unit', $mealItem->portion_unit ?? 'g') === 'g' ? 'selected' : '' }}>{{ __('app.units.g') }}</option>
            <option value="ml" {{ old('portion_unit', $mealItem->portion_unit ?? '') === 'ml' ? 'selected' : '' }}>{{ __('app.units.ml') }}</option>
            <option value="pcs" {{ old('portion_unit', $mealItem->portion_unit ?? '') === 'pcs' ? 'selected' : '' }}>{{ __('app.units.pcs') }}</option>
        </select>
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.notes') }}</label>
        <textarea
            name="notes"
            rows="2"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="{{ __('app.common.notes_placeholder') }}"
        >{{ old('notes', $mealItem->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <button
        type="submit"
        class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700"
    >
        {{ __('app.common.save') }}
    </button>
    <a
        href="{{ route('menu-plans.show', $menuMeal->menuDay->menuPlan ?? $mealItem->menuMeal->menuDay->menuPlan) }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        {{ __('app.common.cancel') }}
    </a>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
