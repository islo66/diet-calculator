@php($v = $nutrient)

<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-8">
        <label class="block text-sm font-medium text-gray-700 mb-1">Food</label>
        <select name="food_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @php($selectedFood = old('food_id', $v?->food_id ?? $foodId))
            @foreach ($foods as $f)
                <option value="{{ $f->id }}" {{ (string)$selectedFood === (string)$f->id ? 'selected' : '' }}>
                    {{ $f->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Basis qty</label>
        <input type="text" name="basis_qty" value="{{ old('basis_qty', $v?->basis_qty ?? 100) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Basis unit</label>
        <input type="text" name="basis_unit" value="{{ old('basis_unit', $v?->basis_unit ?? 'g') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Kcal</label>
        <input type="text" name="kcal" value="{{ old('kcal', $v?->kcal) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Protein (g)</label>
        <input type="text" name="protein_g" value="{{ old('protein_g', $v?->protein_g) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Fat (g)</label>
        <input type="text" name="fat_g" value="{{ old('fat_g', $v?->fat_g) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Carbs (g)</label>
        <input type="text" name="carb_g" value="{{ old('carb_g', $v?->carb_g) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-12">
    <div class="md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Sodium (mg)</label>
        <input type="text" name="sodium_mg" value="{{ old('sodium_mg', $v?->sodium_mg) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Potassium (mg)</label>
        <input type="text" name="potassium_mg" value="{{ old('potassium_mg', $v?->potassium_mg) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
    <div class="md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Phosphorus (mg)</label>
        <input type="text" name="phosphorus_mg" value="{{ old('phosphorus_mg', $v?->phosphorus_mg) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>
</div>
