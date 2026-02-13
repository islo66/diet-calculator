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
            value="{{ old('name', $mealType->name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Ex: Mic dejun, Gustare, Cina tarzie"
            required
        >
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.meal_types.display_order') }} *</label>
        <input
            type="number"
            name="default_sort_order"
            value="{{ old('default_sort_order', $mealType->default_sort_order ?? 10) }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            min="0"
            max="100"
            required
        >
        <p class="mt-1 text-xs text-gray-500">{{ __('app.meal_types.display_order_help') }}</p>
    </div>

    <div class="col-span-12">
        <label class="inline-flex items-center">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                {{ old('is_active', $mealType->is_active ?? true) ? 'checked' : '' }}
            >
            <span class="ml-2 text-sm text-gray-700">{{ __('app.common.active') }}</span>
        </label>
        <p class="mt-1 text-xs text-gray-500">{{ __('app.meal_types.active_help') }}</p>
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
        href="{{ route('meal-types.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>
