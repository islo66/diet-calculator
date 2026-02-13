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
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_days.name') }} *</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $menuDay->name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Ex: Luni, Zi 1, Saptamana 1 - Luni"
            required
        >
    </div>

    @if(!isset($menuDay))
        <div class="col-span-12">
            <label class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="create_default_meals"
                    value="1"
                    checked
                    class="rounded border-gray-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                <span class="text-sm text-gray-700">{{ __('app.menu_days.create_standard_meals') }}</span>
            </label>
        </div>
    @endif

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.notes') }}</label>
        <textarea
            name="notes"
            rows="3"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Note optionale despre aceasta zi..."
        >{{ old('notes', $menuDay->notes ?? '') }}</textarea>
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
        href="{{ route('menu-plans.show', $menuPlan ?? $menuDay->menuPlan) }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>
