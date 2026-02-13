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
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.recipes.name') }} *</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $recipe->name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="{{ __('app.recipes.name_placeholder') }}"
            required
        >
    </div>

    <div class="col-span-6 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.recipes.total_quantity') }}</label>
        @if(isset($recipe) && $recipe->exists)
            <div class="w-full px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-gray-700">
                {{ number_format($recipe->yield_qty, 1) }} {{ $recipe->yield_unit }}
            </div>
            <p class="mt-1 text-xs text-gray-500">{{ __('app.recipes.total_auto') }}</p>
        @else
            <div class="w-full px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-gray-500 italic">
                {{ __('app.recipes.total_after_ingredients') }}
            </div>
        @endif
    </div>

    <div class="col-span-6 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.foods.unit') }} *</label>
        <select
            name="yield_unit"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
            <option value="g" {{ old('yield_unit', $recipe->yield_unit ?? 'g') === 'g' ? 'selected' : '' }}>{{ __('app.units.g_long') }}</option>
            <option value="ml" {{ old('yield_unit', $recipe->yield_unit ?? '') === 'ml' ? 'selected' : '' }}>{{ __('app.units.ml_long') }}</option>
        </select>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.recipes.patient_optional') }}</label>
        <select
            name="patient_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
            <option value="">{{ __('app.recipes.global_option') }}</option>
            @foreach ($patients as $p)
                <option value="{{ $p->id }}" {{ old('patient_id', $recipe->patient_id ?? $patientId ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->last_name }} {{ $p->first_name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">{{ __('app.recipes.global_help') }}</p>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.notes') }}</label>
        <textarea
            name="notes"
            rows="2"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="{{ __('app.recipes.notes_placeholder') }}"
        >{{ old('notes', $recipe->notes ?? '') }}</textarea>
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
        href="{{ route('recipes.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>
