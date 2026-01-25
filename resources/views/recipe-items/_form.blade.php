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
    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">Ingredient (Aliment) *</label>
        <select
            name="food_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
            id="food_select"
        >
            <option value="">Selecteaza ingredient...</option>
            @foreach ($foods as $food)
                <option
                    value="{{ $food->id }}"
                    data-unit="{{ $food->default_unit }}"
                    data-kcal="{{ $food->nutrient?->kcal ?? 0 }}"
                    {{ old('food_id', $recipeItem->food_id ?? '') == $food->id ? 'selected' : '' }}
                >
                    {{ $food->name }}
                    @if($food->nutrient)
                        ({{ number_format($food->nutrient->kcal ?? 0, 0) }} kcal / 100{{ $food->default_unit }})
                    @endif
                </option>
            @endforeach
        </select>
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

<script>
document.getElementById('food_select').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const unit = selected.dataset.unit;
    if (unit) {
        document.getElementById('unit_select').value = unit;
    }
});
</script>