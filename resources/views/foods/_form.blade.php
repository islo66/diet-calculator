@php
    $categoryOptions = $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray();
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

<div class="grid grid-cols-12 gap-4">
    <div class="col-span-12 md:col-span-8">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.table.name') }} *</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $food->name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Ex: Lapte, Paine, Morcovi"
            required
        >
    </div>

    <div class="col-span-6 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.foods.default_unit') }} *</label>
        <select
            name="default_unit"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
            <option value="g" {{ old('default_unit', $food->default_unit ?? 'g') === 'g' ? 'selected' : '' }}>{{ __('app.units.g') }}</option>
            <option value="ml" {{ old('default_unit', $food->default_unit ?? '') === 'ml' ? 'selected' : '' }}>{{ __('app.units.ml') }}</option>
            <option value="pcs" {{ old('default_unit', $food->default_unit ?? '') === 'pcs' ? 'selected' : '' }}>{{ __('app.units.pcs') }}</option>
        </select>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.foods.category') }}</label>
        <x-searchable-select
            name="category_id"
            :options="$categoryOptions"
            :value="old('category_id', $food->category_id ?? '')"
            placeholder="Selecteaza categorie..."
        />
    </div>

    <div class="col-span-6 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.foods.density') }} (g/ml)</label>
        <input
            type="number"
            name="density_g_per_ml"
            value="{{ old('density_g_per_ml', $food->density_g_per_ml ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Ex: 1.03"
            step="0.001"
            min="0.001"
        >
        <p class="mt-1 text-xs text-gray-500">{{ __('app.foods.density_help') }}</p>
    </div>

    <div class="col-span-6 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.status') }}</label>
        <label class="inline-flex items-center mt-2">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                {{ old('is_active', $food->is_active ?? true) ? 'checked' : '' }}
            >
            <span class="ml-2 text-sm text-gray-700">{{ __('app.common.active') }}</span>
        </label>
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.notes') }}</label>
        <textarea
            name="notes"
            rows="2"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Note optionale despre aliment..."
        >{{ old('notes', $food->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <button
        type="submit"
        class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700"
    >
        Salveaza
    </button>
    <a
        href="{{ isset($food) && $food->exists ? route('foods.show', $food) : route('foods.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>
