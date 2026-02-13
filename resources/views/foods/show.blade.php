<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('foods.index')">{{ __('app.foods.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $food->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $food->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $food->category?->name ?? 'Fara categorie' }}
                    &middot; Unitate: {{ $food->default_unit }}
                    @if(!$food->is_active)
                        &middot; <span class="text-red-600">{{ __('app.common.inactive') }}</span>
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('nutrients.create', ['food_id' => $food->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                    Adauga Nutrienti
                </a>
                <a href="{{ route('foods.edit', $food) }}"
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

            {{-- Valori Nutritionale --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Valori Nutritionale
                            <span class="text-sm font-normal text-gray-500">({{ $nutrients->total() }})</span>
                        </h3>
                        <a href="{{ route('nutrients.create', ['food_id' => $food->id]) }}" class="text-emerald-600 hover:underline text-sm">
                            + Adauga
                        </a>
                    </div>
                </div>

                @if($nutrients->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left font-semibold text-gray-700">{{ __('app.nutrients.basis') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.kcal_short') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.protein') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.fat') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.carb') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.fiber') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.sodium_short') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.potassium_short') }}</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-700">{{ __('app.nutrients.phosphorus_short') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @foreach ($nutrients as $nutrient)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium">
                                        {{ $nutrient->basis_qty }}{{ $nutrient->basis_unit }}
                                    </td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->kcal ?? 0, 0) }}</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->protein_g ?? 0, 1) }}g</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->fat_g ?? 0, 1) }}g</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->carb_g ?? 0, 1) }}g</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->fiber_g ?? 0, 1) }}g</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->sodium_mg ?? 0, 0) }}mg</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->potassium_mg ?? 0, 0) }}mg</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($nutrient->phosphorus_mg ?? 0, 0) }}mg</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <a href="{{ route('nutrients.edit', $nutrient) }}" class="text-emerald-600 hover:underline">{{ __('app.common.edit') }}</a>
                                        <form method="POST" action="{{ route('nutrients.destroy', $nutrient) }}" class="inline ml-2" onsubmit="return confirm('Stergi aceste valori nutritionale?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">X</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($nutrients->hasPages())
                        <div class="mt-4">
                            {{ $nutrients->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-gray-500">
                        <p>{{ __('app.nutrients.empty') }}</p>
                        <a href="{{ route('nutrients.create', ['food_id' => $food->id]) }}" class="mt-2 inline-block text-emerald-600 hover:underline">
                            Adauga valori nutritionale
                        </a>
                    </div>
                @endif
            </div>

            {{-- Detalii --}}
            @if($food->notes || $food->density_g_per_ml)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.common.details') }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @if($food->density_g_per_ml)
                            <div>
                                <span class="text-gray-500">{{ __('app.foods.density') }}:</span>
                                <span class="ml-2 font-medium">{{ $food->density_g_per_ml }} g/ml</span>
                            </div>
                        @endif
                    </div>
                    @if($food->notes)
                        <div class="mt-4">
                            <span class="text-gray-500">{{ __('app.common.notes') }}:</span>
                            <p class="mt-1 text-gray-700">{{ $food->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Sterge --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('app.danger_zone.title') }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ __('app.danger_zone.foods_warning') }}</p>
                <form method="POST" action="{{ route('foods.destroy', $food) }}" onsubmit="return confirm('Esti sigur ca vrei sa stergi acest aliment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Sterge Alimentul
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
