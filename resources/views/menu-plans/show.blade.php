<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('menu-plans.index')">{{ __('app.menu_plans.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $menuPlan->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $menuPlan->name }}
                <span class="text-gray-500 text-sm font-normal">
                    - {{ $menuPlan->patient->full_name }}
                </span>
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('menu-plans.pdf', $menuPlan) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    PDF
                </a>
                <a href="{{ route('menu-plans.word', $menuPlan) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    WORD
                </a>
                <a href="{{ route('menu-days.create', $menuPlan) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                    {{ __('app.menu_days.add') }}
                </a>
                <a href="{{ route('menu-plans.edit', $menuPlan) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    {{ __('app.menu_plans.edit') }}
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

            {{-- Informatii Pacient --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.menu_plans.patient_daily_limits') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-500 block text-xs uppercase">{{ __('app.nutrients.calories') }}</span>
                            <span class="font-medium text-lg">{{ $menuPlan->patient->target_kcal_per_day ?? '—' }}</span>
                            <span class="text-gray-500 text-xs">kcal</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-500 block text-xs uppercase">{{ __('app.nutrients.protein') }}</span>
                            <span class="font-medium text-lg">{{ $menuPlan->patient->target_protein_g_per_day ?? '—' }}</span>
                            <span class="text-gray-500 text-xs">g</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-500 block text-xs uppercase">{{ __('app.nutrients.sodium') }}</span>
                            <span class="font-medium text-lg">{{ $menuPlan->patient->limit_sodium_mg_per_day ?? '—' }}</span>
                            <span class="text-gray-500 text-xs">mg</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-500 block text-xs uppercase">{{ __('app.nutrients.potassium') }}</span>
                            <span class="font-medium text-lg">{{ $menuPlan->patient->limit_potassium_mg_per_day ?? '—' }}</span>
                            <span class="text-gray-500 text-xs">mg</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <span class="text-gray-500 block text-xs uppercase">{{ __('app.nutrients.phosphorus') }}</span>
                            <span class="font-medium text-lg">{{ $menuPlan->patient->limit_phosphorus_mg_per_day ?? '—' }}</span>
                            <span class="text-gray-500 text-xs">mg</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Zile --}}
            @foreach ($daysWithNutrients as $dayData)
                @php
                    $day = $dayData['day'];
                    $nutrients = $dayData['nutrients'];
                    $statusColors = [
                        'ok' => 'bg-green-100 text-green-800',
                        'under' => 'bg-yellow-100 text-yellow-800',
                        'warning' => 'bg-orange-100 text-orange-800',
                        'over' => 'bg-red-100 text-red-800',
                    ];
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                     x-data="{
                        open: localStorage.getItem('day_collapse_{{ $day->id }}') !== 'closed',
                        toggle() {
                            this.open = !this.open;
                            localStorage.setItem('day_collapse_{{ $day->id }}', this.open ? 'open' : 'closed');
                        }
                     }">
                    <div class="p-6">
                        {{-- Header Zi --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <button @click="toggle()" class="text-gray-500 hover:text-gray-700 focus:outline-none transition-transform duration-200" :class="{ 'rotate-90': open }">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <h3 class="text-lg font-medium text-gray-900 cursor-pointer" @click="toggle()">{{ $day->name }}</h3>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('menu-days.edit', $day) }}" class="text-emerald-600 hover:underline text-sm">{{ __('app.common.edit') }}</a>
                                <form method="POST" action="{{ route('menu-days.destroy', $day) }}" class="inline" onsubmit="return confirm('{{ __('app.menu_days.delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-sm">{{ __('app.common.delete') }}</button>
                                </form>
                            </div>
                        </div>

                        {{-- Sumar Nutrienti Zi --}}
                        <div class="bg-gray-50 rounded-lg p-4" :class="{ 'mb-4': open }">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('app.menu_days.total_day') }}</h4>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @php
                                    $nutrientLabels = [
                                        'kcal' => __('app.nutrients.calories'),
                                        'protein_g' => __('app.nutrients.protein_g'),
                                        'sodium_mg' => __('app.nutrients.sodium_mg'),
                                        'potassium_mg' => __('app.nutrients.potassium_mg'),
                                        'phosphorus_mg' => __('app.nutrients.phosphorus_mg'),
                                    ];
                                @endphp
                                @foreach($nutrientLabels as $nutrient => $label)
                                    @php
                                        $value = $nutrients['totals'][$nutrient] ?? 0;
                                        $limit = $nutrients['limits'][$nutrient] ?? 0;
                                        $status = $nutrients['comparison'][$nutrient] ?? 'ok';
                                    @endphp
                                    <div>
                                        <div class="text-xs text-gray-500 uppercase mb-1">{{ $label }}</div>
                                        <div class="flex items-baseline gap-1">
                                            <span class="font-medium {{ $statusColors[$status] }} px-2 py-1 rounded text-sm">
                                                {{ number_format($value, $nutrient === 'kcal' ? 0 : 1) }}
                                            </span>
                                            @if($limit > 0)
                                                <span class="text-gray-400 text-xs">/ {{ number_format($limit, 0) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Mese --}}
                        <div class="space-y-4 mt-4" x-show="open" x-collapse>
                            @foreach ($day->meals as $meal)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h5 class="font-medium text-gray-800">{{ $meal->name }}</h5>
                                        <a href="{{ route('meal-items.create', $meal) }}"
                                           class="text-emerald-600 hover:underline text-sm">
                                            + {{ __('app.meal_items.add') }}
                                        </a>
                                    </div>

                                    @if($meal->items->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead>
                                                <tr class="text-left text-gray-500 text-xs uppercase">
                                                    <th class="pb-2 pr-4">{{ __('app.foods.title') }}</th>
                                                    <th class="pb-2 pr-4">{{ __('app.meal_items.portion') }}</th>
                                                    <th class="pb-2 pr-4 text-right">{{ __('app.nutrients.kcal_short') }}</th>
                                                    <th class="pb-2 pr-4 text-right">{{ __('app.nutrients.protein_short') }}</th>
                                                    <th class="pb-2 pr-4 text-right">{{ __('app.nutrients.sodium_short') }}</th>
                                                    <th class="pb-2 pr-4 text-right">{{ __('app.nutrients.potassium_short') }}</th>
                                                    <th class="pb-2 pr-4 text-right">{{ __('app.nutrients.phosphorus_short') }}</th>
                                                    <th class="pb-2"></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($meal->items as $item)
                                                    @php($itemNutrients = $item->calculateNutrients())
                                                    <tr class="border-t">
                                                        <td class="py-2 pr-4">{{ $item->name }}</td>
                                                        <td class="py-2 pr-4 text-gray-600">{{ $item->portion_qty }} {{ $item->portion_unit }}</td>
                                                        <td class="py-2 pr-4 text-right">{{ number_format($itemNutrients['kcal'], 0) }}</td>
                                                        <td class="py-2 pr-4 text-right">{{ number_format($itemNutrients['protein_g'], 1) }}</td>
                                                        <td class="py-2 pr-4 text-right">{{ number_format($itemNutrients['sodium_mg'], 0) }}</td>
                                                        <td class="py-2 pr-4 text-right">{{ number_format($itemNutrients['potassium_mg'], 0) }}</td>
                                                        <td class="py-2 pr-4 text-right">{{ number_format($itemNutrients['phosphorus_mg'], 0) }}</td>
                                                        <td class="py-2 text-right whitespace-nowrap">
                                                            <a href="{{ route('meal-items.edit', $item) }}" class="text-emerald-600 hover:underline">{{ __('app.common.edit') }}</a>
                                                            <form method="POST" action="{{ route('meal-items.destroy', $item) }}" class="inline ml-2" onsubmit="return confirm('{{ __('app.meal_items.delete_confirm') }}')">
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
                                    @else
                                        <p class="text-gray-500 text-sm italic">{{ __('app.meal_items.empty') }}</p>
                                    @endif
                                </div>
                            @endforeach

                            @if($day->meals->count() === 0)
                                <p class="text-gray-500 text-sm italic">{{ __('app.meal_items.no_meals') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if($days->total() === 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        {{ __('app.menu_days.empty') }}
                        <a href="{{ route('menu-days.create', $menuPlan) }}" class="text-emerald-600 hover:underline">
                            {{ __('app.menu_days.add_first') }}
                        </a>
                    </div>
                </div>
            @endif

            @if($days->hasPages())
                <div class="mt-4">
                    {{ $days->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
