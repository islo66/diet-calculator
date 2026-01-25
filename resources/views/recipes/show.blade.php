<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('recipes.index')">Retete</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $recipe->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $recipe->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Produce {{ number_format($recipe->yield_qty, 0) }}{{ $recipe->yield_unit }}
                    @if($recipe->patient)
                        &middot; Pentru: {{ $recipe->patient->full_name }}
                    @else
                        &middot; Reteta globala
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('recipe-items.create', $recipe) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Adauga Ingredient
                </a>
                <a href="{{ route('recipes.edit', $recipe) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    Editeaza
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Sumar Nutrienti --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nutrienti per 100g --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nutrienti per 100{{ $recipe->yield_unit }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-orange-50 rounded-lg p-3">
                            <span class="text-xs text-orange-600 uppercase font-medium">Calorii</span>
                            <div class="text-2xl font-bold text-orange-700">{{ number_format($nutrientsPer100['kcal'], 0) }}</div>
                            <span class="text-xs text-orange-600">kcal</span>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3">
                            <span class="text-xs text-red-600 uppercase font-medium">Proteine</span>
                            <div class="text-2xl font-bold text-red-700">{{ number_format($nutrientsPer100['protein_g'], 1) }}</div>
                            <span class="text-xs text-red-600">g</span>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3">
                            <span class="text-xs text-yellow-600 uppercase font-medium">Grasimi</span>
                            <div class="text-2xl font-bold text-yellow-700">{{ number_format($nutrientsPer100['fat_g'], 1) }}</div>
                            <span class="text-xs text-yellow-600">g</span>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3">
                            <span class="text-xs text-blue-600 uppercase font-medium">Carbohidrati</span>
                            <div class="text-2xl font-bold text-blue-700">{{ number_format($nutrientsPer100['carb_g'], 1) }}</div>
                            <span class="text-xs text-blue-600">g</span>
                        </div>
                    </div>
                </div>

                {{-- Minerale per 100g --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Minerale per 100{{ $recipe->yield_unit }}</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-xs text-gray-600 uppercase font-medium">Sodiu (Na)</span>
                            <div class="text-xl font-bold text-gray-700">{{ number_format($nutrientsPer100['sodium_mg'], 0) }}</div>
                            <span class="text-xs text-gray-600">mg</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-xs text-gray-600 uppercase font-medium">Potasiu (K)</span>
                            <div class="text-xl font-bold text-gray-700">{{ number_format($nutrientsPer100['potassium_mg'], 0) }}</div>
                            <span class="text-xs text-gray-600">mg</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-xs text-gray-600 uppercase font-medium">Fosfor (P)</span>
                            <div class="text-xl font-bold text-gray-700">{{ number_format($nutrientsPer100['phosphorus_mg'], 0) }}</div>
                            <span class="text-xs text-gray-600">mg</span>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-indigo-50 rounded-lg">
                        <span class="text-xs text-indigo-600 uppercase font-medium">Total Reteta ({{ number_format($recipe->yield_qty, 0) }}{{ $recipe->yield_unit }})</span>
                        <div class="grid grid-cols-3 gap-2 mt-2 text-sm">
                            <div><span class="text-gray-600">Na:</span> <strong>{{ number_format($totalNutrients['sodium_mg'], 0) }}mg</strong></div>
                            <div><span class="text-gray-600">K:</span> <strong>{{ number_format($totalNutrients['potassium_mg'], 0) }}mg</strong></div>
                            <div><span class="text-gray-600">P:</span> <strong>{{ number_format($totalNutrients['phosphorus_mg'], 0) }}mg</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ingrediente --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Ingrediente
                            <span class="text-sm font-normal text-gray-500">({{ $recipe->items->count() }})</span>
                        </h3>
                        <a href="{{ route('recipe-items.create', $recipe) }}" class="text-indigo-600 hover:underline text-sm">
                            + Adauga Ingredient
                        </a>
                    </div>
                </div>

                @if($recipe->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Ingredient</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">Cantitate</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Kcal</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Prot</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">Na</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">K</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">P</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @foreach ($recipe->items as $item)
                                @php($itemNutrients = $item->calculateNutrients())
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->food?->name ?? 'Unknown' }}</div>
                                        @if($item->notes)
                                            <div class="text-xs text-gray-500">{{ $item->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ number_format($item->qty, 1) }} {{ $item->unit }}
                                    </td>
                                    <td class="px-6 py-4 text-right">{{ number_format($itemNutrients['kcal'], 0) }}</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($itemNutrients['protein_g'], 1) }}g</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($itemNutrients['sodium_mg'], 0) }}</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($itemNutrients['potassium_mg'], 0) }}</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($itemNutrients['phosphorus_mg'], 0) }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('recipe-items.edit', $item) }}" class="text-indigo-600 hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('recipe-items.destroy', $item) }}" class="inline ml-2" onsubmit="return confirm('Stergi acest ingredient?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">X</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                            <tr class="font-semibold">
                                <td class="px-6 py-3" colspan="2">TOTAL</td>
                                <td class="px-6 py-3 text-right">{{ number_format($totalNutrients['kcal'], 0) }}</td>
                                <td class="px-6 py-3 text-right">{{ number_format($totalNutrients['protein_g'], 1) }}g</td>
                                <td class="px-6 py-3 text-right">{{ number_format($totalNutrients['sodium_mg'], 0) }}</td>
                                <td class="px-6 py-3 text-right">{{ number_format($totalNutrients['potassium_mg'], 0) }}</td>
                                <td class="px-6 py-3 text-right">{{ number_format($totalNutrients['phosphorus_mg'], 0) }}</td>
                                <td class="px-6 py-3"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p>Niciun ingredient adaugat.</p>
                        <a href="{{ route('recipe-items.create', $recipe) }}" class="mt-2 inline-block text-indigo-600 hover:underline">
                            Adauga primul ingredient
                        </a>
                    </div>
                @endif
            </div>

            {{-- Note --}}
            @if($recipe->notes)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Note</h3>
                    <p class="text-gray-600">{{ $recipe->notes }}</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>