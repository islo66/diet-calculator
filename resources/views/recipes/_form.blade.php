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
        <label class="block text-sm font-medium text-gray-700 mb-1">Nume Reteta *</label>
        <input
            type="text"
            name="name"
            value="{{ old('name', $recipe->name ?? '') }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Ex: Ciorba de legume, Piure de cartofi"
            required
        >
    </div>

    <div class="col-span-6 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Cantitate Totala</label>
        @if(isset($recipe) && $recipe->exists)
            <div class="w-full px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-gray-700">
                {{ number_format($recipe->yield_qty, 1) }} {{ $recipe->yield_unit }}
            </div>
            <p class="mt-1 text-xs text-gray-500">Calculata automat din ingrediente</p>
        @else
            <div class="w-full px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-gray-500 italic">
                Se calculeaza dupa adaugarea ingredientelor
            </div>
        @endif
    </div>

    <div class="col-span-6 md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Unitate *</label>
        <select
            name="yield_unit"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            required
        >
            <option value="g" {{ old('yield_unit', $recipe->yield_unit ?? 'g') === 'g' ? 'selected' : '' }}>grame (g)</option>
            <option value="ml" {{ old('yield_unit', $recipe->yield_unit ?? '') === 'ml' ? 'selected' : '' }}>mililitri (ml)</option>
        </select>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Pacient (optional)</label>
        <select
            name="patient_id"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        >
            <option value="">Reteta globala (disponibila pentru toti)</option>
            @foreach ($patients as $p)
                <option value="{{ $p->id }}" {{ old('patient_id', $recipe->patient_id ?? $patientId ?? '') == $p->id ? 'selected' : '' }}>
                    {{ $p->last_name }} {{ $p->first_name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Lasa gol pentru retete disponibile tuturor pacientilor</p>
    </div>

    <div class="col-span-12 md:col-span-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
        <textarea
            name="notes"
            rows="2"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Instructiuni de preparare, variante, etc..."
        >{{ old('notes', $recipe->notes ?? '') }}</textarea>
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
        href="{{ route('recipes.index') }}"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
    >
        Anuleaza
    </a>
</div>
