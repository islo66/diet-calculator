@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-12 gap-4">
    {{-- Tip item --}}
    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">Tip *</label>
        <div class="flex gap-4">
            <label class="inline-flex items-center">
                <input
                    type="radio"
                    name="item_type"
                    value="food"
                    class="form-radio text-indigo-600"
                    {{ old('item_type', $mealItem->item_type ?? 'food') === 'food' ? 'checked' : '' }}
                    onchange="toggleItemType()"
                >
                <span class="ml-2">Aliment</span>
            </label>
            <label class="inline-flex items-center">
                <input
                    type="radio"
                    name="item_type"
                    value="recipe"
                    class="form-radio text-indigo-600"
                    {{ old('item_type', $mealItem->item_type ?? '') === 'recipe' ? 'checked' : '' }}
                    onchange="toggleItemType()"
                >
                <span class="ml-2">Reteta / Mancare</span>
            </label>
        </div>
    </div>

    {{-- Selector Aliment --}}
    <div class="col-span-12" id="food_section">
        <label class="block text-sm font-medium text-gray-700 mb-1">Aliment *</label>
        <select
            name="food_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            id="food_select"
        >
            <option value="">Selecteaza aliment...</option>
            @foreach ($foods as $food)
                <option
                    value="{{ $food->id }}"
                    data-unit="{{ $food->default_unit }}"
                    data-kcal="{{ $food->nutrient?->kcal ?? 0 }}"
                    data-protein="{{ $food->nutrient?->protein_g ?? 0 }}"
                    {{ old('food_id', $mealItem->food_id ?? '') == $food->id ? 'selected' : '' }}
                >
                    {{ $food->name }}
                    @if($food->nutrient)
                        ({{ number_format($food->nutrient->kcal ?? 0, 0) }} kcal / 100{{ $food->default_unit }})
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    {{-- Selector Reteta --}}
    <div class="col-span-12" id="recipe_section" style="display: none;">
        <label class="block text-sm font-medium text-gray-700 mb-1">Reteta / Mancare *</label>
        <select
            name="recipe_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            id="recipe_select"
        >
            <option value="">Selecteaza reteta...</option>
            @foreach ($recipes as $recipe)
                @php
                    $totalNutrients = $recipe->calculateTotalNutrients();
                    $per100Kcal = $recipe->yield_qty > 0 ? ($totalNutrients['kcal'] / $recipe->yield_qty) * 100 : 0;
                @endphp
                <option
                    value="{{ $recipe->id }}"
                    data-unit="{{ $recipe->yield_unit }}"
                    data-yield="{{ $recipe->yield_qty }}"
                    {{ old('recipe_id', $mealItem->recipe_id ?? '') == $recipe->id ? 'selected' : '' }}
                >
                    {{ $recipe->name }}
                    ({{ number_format($per100Kcal, 0) }} kcal/100{{ $recipe->yield_unit }}, total {{ $recipe->yield_qty }}{{ $recipe->yield_unit }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Cantitate *</label>
        <input
            type="number"
            name="portion_qty"
            value="{{ old('portion_qty', $mealItem->portion_qty ?? 100) }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            step="0.001"
            min="0.001"
            required
        >
    </div>

    <div class="col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Unitate *</label>
        <select
            name="portion_unit"
            id="portion_unit"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            <option value="g" {{ old('portion_unit', $mealItem->portion_unit ?? 'g') === 'g' ? 'selected' : '' }}>g (grame)</option>
            <option value="ml" {{ old('portion_unit', $mealItem->portion_unit ?? '') === 'ml' ? 'selected' : '' }}>ml (mililitri)</option>
            <option value="pcs" {{ old('portion_unit', $mealItem->portion_unit ?? '') === 'pcs' ? 'selected' : '' }}>pcs (bucati)</option>
        </select>
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <textarea
            name="notes"
            rows="2"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Note optionale..."
        >{{ old('notes', $mealItem->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <button
        type="submit"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
    >
        Salveaza
    </button>
    <a
        href="{{ route('menu-plans.show', $menuMeal->menuDay->menuPlan ?? $mealItem->menuMeal->menuDay->menuPlan) }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>

<script>
function toggleItemType() {
    const itemType = document.querySelector('input[name="item_type"]:checked').value;
    const foodSection = document.getElementById('food_section');
    const recipeSection = document.getElementById('recipe_section');
    const foodSelect = document.getElementById('food_select');
    const recipeSelect = document.getElementById('recipe_select');

    if (itemType === 'food') {
        foodSection.style.display = 'block';
        recipeSection.style.display = 'none';
        foodSelect.required = true;
        recipeSelect.required = false;
    } else {
        foodSection.style.display = 'none';
        recipeSection.style.display = 'block';
        foodSelect.required = false;
        recipeSelect.required = true;
    }
}

// Inițializare la încărcare
document.addEventListener('DOMContentLoaded', function() {
    toggleItemType();
});

// Auto-selectare unitate la schimbarea alimentului
document.getElementById('food_select').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const unit = selected.dataset.unit;
    if (unit) {
        document.getElementById('portion_unit').value = unit;
    }
});

// Auto-selectare unitate la schimbarea rețetei
document.getElementById('recipe_select').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const unit = selected.dataset.unit;
    if (unit) {
        document.getElementById('portion_unit').value = unit;
    }
});
</script>