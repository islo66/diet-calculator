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
    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_plans.patient') }} *</label>
        <select
            name="patient_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
            <option value="">{{ __('app.menu_plans.select_patient') }}</option>
            @foreach ($patients as $p)
                <option value="{{ $p->id }}" {{ old('patient_id', $menuPlan->patient_id ?? $patientId ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->last_name }} {{ $p->first_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_plans.name') }} *</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $menuPlan->name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Ex: Meniu saptamana 1"
            required
        >
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_plans.start_date') }}</label>
        <input
            type="date"
            name="starts_at"
            value="{{ old('starts_at', isset($menuPlan) && $menuPlan->starts_at ? $menuPlan->starts_at->format('Y-m-d') : '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_plans.end_date') }}</label>
        <input
            type="date"
            name="ends_at"
            value="{{ old('ends_at', isset($menuPlan) && $menuPlan->ends_at ? $menuPlan->ends_at->format('Y-m-d') : '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.active') }}</label>
        <select
            name="is_active"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
            <option value="1" {{ old('is_active', $menuPlan->is_active ?? true) ? 'selected' : '' }}>{{ __('app.common.yes') }}</option>
            <option value="0" {{ old('is_active', $menuPlan->is_active ?? true) ? '' : 'selected' }}>{{ __('app.common.no') }}</option>
        </select>
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.notes') }}</label>
        <textarea
            name="notes"
            rows="3"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="Note optionale despre acest plan..."
        >{{ old('notes', $menuPlan->notes ?? '') }}</textarea>
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
        href="{{ route('menu-plans.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>
