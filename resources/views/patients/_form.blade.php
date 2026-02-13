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
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.first_name') }} *</label>
        <input
            type="text"
            name="first_name"
            value="{{ old('first_name', $patient->first_name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.last_name') }} *</label>
        <input
            type="text"
            name="last_name"
            value="{{ old('last_name', $patient->last_name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.sex') }}</label>
        <select
            name="sex"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
            <option value="">{{ __('app.common.unknown') }}</option>
            <option value="M" {{ old('sex', $patient->sex ?? '') === 'M' ? 'selected' : '' }}>{{ __('app.patients.sex_m') }}</option>
            <option value="F" {{ old('sex', $patient->sex ?? '') === 'F' ? 'selected' : '' }}>{{ __('app.patients.sex_f') }}</option>
        </select>
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.birthdate') }}</label>
        <input
            type="date"
            name="birthdate"
            value="{{ old('birthdate', isset($patient) && $patient->birthdate ? $patient->birthdate->format('Y-m-d') : '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
    </div>

    <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.status') }}</label>
        <select
            name="is_active"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
            <option value="1" {{ old('is_active', $patient->is_active ?? true) ? 'selected' : '' }}>{{ __('app.common.active') }}</option>
            <option value="0" {{ old('is_active', $patient->is_active ?? true) ? '' : 'selected' }}>{{ __('app.common.inactive') }}</option>
        </select>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.height') }}</label>
        <input
            type="number"
            name="current_height_cm"
            value="{{ old('current_height_cm', $patient->current_height_cm ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            min="50"
            max="300"
            step="1"
        >
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.weight') }}</label>
        <input
            type="number"
            name="current_weight_kg"
            value="{{ old('current_weight_kg', $patient->current_weight_kg ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            min="1"
            max="500"
            step="0.01"
        >
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.diagnosis') }}</label>
        <input
            type="text"
            name="diagnosis"
            value="{{ old('diagnosis', $patient->diagnosis ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            maxlength="255"
        >
    </div>

    <div class="col-span-12">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ __('app.patients.daily_targets') }}</h3>
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.target_kcal') }}</label>
        <input type="number" name="target_kcal_per_day" value="{{ old('target_kcal_per_day', $patient->target_kcal_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="10000">
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.target_protein') }}</label>
        <input type="number" name="target_protein_g_per_day" value="{{ old('target_protein_g_per_day', $patient->target_protein_g_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="1000" step="0.01">
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.target_carbs') }}</label>
        <input type="number" name="target_carbs_g_per_day" value="{{ old('target_carbs_g_per_day', $patient->target_carbs_g_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="1000" step="0.01">
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.target_fat') }}</label>
        <input type="number" name="target_fat_g_per_day" value="{{ old('target_fat_g_per_day', $patient->target_fat_g_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="1000" step="0.01">
    </div>

    <div class="col-span-12">
        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">{{ __('app.patients.daily_limits') }}</h3>
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.limit_sodium') }}</label>
        <input type="number" name="limit_sodium_mg_per_day" value="{{ old('limit_sodium_mg_per_day', $patient->limit_sodium_mg_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="20000">
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.limit_potassium') }}</label>
        <input type="number" name="limit_potassium_mg_per_day" value="{{ old('limit_potassium_mg_per_day', $patient->limit_potassium_mg_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="20000">
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.limit_phosphorus') }}</label>
        <input type="number" name="limit_phosphorus_mg_per_day" value="{{ old('limit_phosphorus_mg_per_day', $patient->limit_phosphorus_mg_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="20000">
    </div>

    <div class="col-span-12 md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.patients.limit_fluids') }}</label>
        <input type="number" name="limit_fluids_ml_per_day" value="{{ old('limit_fluids_ml_per_day', $patient->limit_fluids_ml_per_day ?? '') }}"
               class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" max="20000">
    </div>

    <div class="col-span-12">
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.notes') }}</label>
        <textarea
            name="notes"
            rows="3"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            placeholder="{{ __('app.common.notes_placeholder') }}"
        >{{ old('notes', $patient->notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex items-center gap-4">
    <button
        type="submit"
        class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700"
    >
        {{ __('app.common.save') }}
    </button>
    <a
        href="{{ isset($patient) && $patient->exists ? route('patients.show', $patient) : route('patients.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        {{ __('app.common.cancel') }}
    </a>
</div>
