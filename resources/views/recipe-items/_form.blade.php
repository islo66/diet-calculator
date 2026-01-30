@php
    $foodOptions = $foods->map(function($food) {
        $kcal = $food->nutrient?->kcal ?? 0;
        return [
            'id' => $food->id,
            'name' => $food->name,
            'subtitle' => $food->nutrient ? number_format($kcal, 0) . ' kcal/100' . $food->default_unit : null,
            'unit' => $food->default_unit,
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
        foodOptions: {{ Js::from($foodOptions) }},
        setUnit(selectedId) {
            const option = this.foodOptions.find(o => o.id == selectedId);
            if (option && option.unit) {
                document.getElementById('unit_select').value = option.unit;
            }
        }
    }"
    class="grid grid-cols-12 gap-4"
>
    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">Ingredient (Aliment) *</label>
        <x-searchable-select
            name="food_id"
            :options="$foodOptions"
            :value="old('food_id', $recipeItem->food_id ?? '')"
            placeholder="Cauta ingredient... (ex: faina, zahar, lapte)"
            required
            @change="setUnit($event.target.value)"
        />
    </div>

    <div class="col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Cantitate *</label>
        <input
            type="number"
            name="qty"
            value="{{ old('qty', $recipeItem->qty ?? 100) }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            step="0.001"
            min="0.001"
            required
        >
    </div>

    <div class="col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Unitate *</label>
        <select
            name="unit"
            id="unit_select"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            <option value="g" {{ old('unit', $recipeItem->unit ?? 'g') === 'g' ? 'selected' : '' }}>g (grame)</option>
            <option value="ml" {{ old('unit', $recipeItem->unit ?? '') === 'ml' ? 'selected' : '' }}>ml (mililitri)</option>
            <option value="pcs" {{ old('unit', $recipeItem->unit ?? '') === 'pcs' ? 'selected' : '' }}>pcs (bucati)</option>
        </select>
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <textarea
            name="notes"
            rows="2"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Ex: tocat marunt, fiert, crud..."
        >{{ old('notes', $recipeItem->notes ?? '') }}</textarea>
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
        href="{{ route('recipes.show', $recipe ?? $recipeItem->recipe) }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>