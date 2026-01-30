@php
    $v = $nutrient ?? null;
    $foodOptions = $foods->map(fn($f) => ['id' => $f->id, 'name' => $f->name])->toArray();
    $selectedFoodId = old('food_id', $v?->food_id ?? $foodId ?? '');
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

<div class="space-y-6">
    {{-- Aliment și Bază --}}
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-12 md:col-span-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Aliment *</label>
            <x-searchable-select
                name="food_id"
                :options="$foodOptions"
                :value="$selectedFoodId"
                placeholder="Cauta aliment..."
                required
            />
        </div>

        <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cantitate Baza *</label>
            <input
                type="number"
                name="basis_qty"
                value="{{ old('basis_qty', $v?->basis_qty ?? 100) }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                step="0.01"
                min="0.01"
                required
            >
        </div>

        <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Unitate Baza *</label>
            <select
                name="basis_unit"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                required
            >
                <option value="g" {{ old('basis_unit', $v?->basis_unit ?? 'g') === 'g' ? 'selected' : '' }}>g (grame)</option>
                <option value="ml" {{ old('basis_unit', $v?->basis_unit ?? '') === 'ml' ? 'selected' : '' }}>ml (mililitri)</option>
                <option value="pcs" {{ old('basis_unit', $v?->basis_unit ?? '') === 'pcs' ? 'selected' : '' }}>pcs (bucati)</option>
            </select>
        </div>
    </div>

    {{-- Macronutrienți --}}
    <div>
        <h4 class="text-sm font-medium text-gray-700 mb-3">Macronutrienti</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-6 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Calorii (kcal)</label>
                <input
                    type="number"
                    name="kcal"
                    value="{{ old('kcal', $v?->kcal) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.1"
                    min="0"
                >
            </div>
            <div class="col-span-6 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Proteine (g)</label>
                <input
                    type="number"
                    name="protein_g"
                    value="{{ old('protein_g', $v?->protein_g) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.01"
                    min="0"
                >
            </div>
            <div class="col-span-6 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Grasimi (g)</label>
                <input
                    type="number"
                    name="fat_g"
                    value="{{ old('fat_g', $v?->fat_g) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.01"
                    min="0"
                >
            </div>
            <div class="col-span-6 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Carbohidrati (g)</label>
                <input
                    type="number"
                    name="carb_g"
                    value="{{ old('carb_g', $v?->carb_g) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.01"
                    min="0"
                >
            </div>
            <div class="col-span-6 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Fibre (g)</label>
                <input
                    type="number"
                    name="fiber_g"
                    value="{{ old('fiber_g', $v?->fiber_g) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.01"
                    min="0"
                >
            </div>
        </div>
    </div>

    {{-- Minerale principale --}}
    <div>
        <h4 class="text-sm font-medium text-gray-700 mb-3">Minerale (importante pentru dieta renala)</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-4 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Sodiu Na (mg)</label>
                <input
                    type="number"
                    name="sodium_mg"
                    value="{{ old('sodium_mg', $v?->sodium_mg) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.1"
                    min="0"
                >
            </div>
            <div class="col-span-4 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Potasiu K (mg)</label>
                <input
                    type="number"
                    name="potassium_mg"
                    value="{{ old('potassium_mg', $v?->potassium_mg) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.1"
                    min="0"
                >
            </div>
            <div class="col-span-4 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Fosfor P (mg)</label>
                <input
                    type="number"
                    name="phosphorus_mg"
                    value="{{ old('phosphorus_mg', $v?->phosphorus_mg) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.1"
                    min="0"
                >
            </div>
        </div>
    </div>

    {{-- Alte minerale --}}
    <div>
        <h4 class="text-sm font-medium text-gray-700 mb-3">Alte minerale</h4>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-4 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Calciu Ca (mg)</label>
                <input
                    type="number"
                    name="calcium_mg"
                    value="{{ old('calcium_mg', $v?->calcium_mg) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.1"
                    min="0"
                >
            </div>
            <div class="col-span-4 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Magneziu Mg (mg)</label>
                <input
                    type="number"
                    name="magnesium_mg"
                    value="{{ old('magnesium_mg', $v?->magnesium_mg) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.1"
                    min="0"
                >
            </div>
            <div class="col-span-4 md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Fier Fe (mg)</label>
                <input
                    type="number"
                    name="iron_mg"
                    value="{{ old('iron_mg', $v?->iron_mg) }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    step="0.01"
                    min="0"
                >
            </div>
        </div>
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
        href="{{ $selectedFoodId ? route('foods.show', $selectedFoodId) : route('nutrients.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>