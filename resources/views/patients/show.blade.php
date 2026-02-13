<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('patients.index')">{{ __('app.patients.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $patient->full_name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $patient->full_name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    @if($patient->diagnosis)
                        {{ $patient->diagnosis }} &middot;
                    @endif
                    {{ __('app.patients.menu_plans') }}: {{ $patient->menu_plans_count }} &middot;
                    {{ __('app.patients.recipes') }}: {{ $patient->recipes_count }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('menu-plans.create', ['patient_id' => $patient->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                    {{ __('app.patients.add_menu_plan') }}
                </a>
                <a href="{{ route('patients.edit', $patient) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    {{ __('app.common.edit') }}
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

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.common.details') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">{{ __('app.patients.sex') }}</div>
                        <div class="font-medium">
                            @if($patient->sex === 'M')
                                {{ __('app.patients.sex_m') }}
                            @elseif($patient->sex === 'F')
                                {{ __('app.patients.sex_f') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('app.patients.birthdate') }}</div>
                        <div class="font-medium">{{ $patient->birthdate?->format('d.m.Y') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('app.common.status') }}</div>
                        <div class="font-medium">{{ $patient->is_active ? __('app.common.active') : __('app.common.inactive') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('app.patients.height') }}</div>
                        <div class="font-medium">{{ $patient->current_height_cm ? $patient->current_height_cm . ' cm' : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('app.patients.weight') }}</div>
                        <div class="font-medium">{{ $patient->current_weight_kg ? $patient->current_weight_kg . ' kg' : '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ __('app.patients.diagnosis') }}</div>
                        <div class="font-medium">{{ $patient->diagnosis ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.patients.daily_targets') }}</h3>
                    <div class="space-y-2 text-sm">
                        <div>{{ __('app.patients.target_kcal') }}: <strong>{{ $patient->target_kcal_per_day ?? '—' }}</strong></div>
                        <div>{{ __('app.patients.target_protein') }}: <strong>{{ $patient->target_protein_g_per_day ?? '—' }}</strong></div>
                        <div>{{ __('app.patients.target_carbs') }}: <strong>{{ $patient->target_carbs_g_per_day ?? '—' }}</strong></div>
                        <div>{{ __('app.patients.target_fat') }}: <strong>{{ $patient->target_fat_g_per_day ?? '—' }}</strong></div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('app.patients.daily_limits') }}</h3>
                    <div class="space-y-2 text-sm">
                        <div>{{ __('app.patients.limit_sodium') }}: <strong>{{ $patient->limit_sodium_mg_per_day ?? '—' }}</strong></div>
                        <div>{{ __('app.patients.limit_potassium') }}: <strong>{{ $patient->limit_potassium_mg_per_day ?? '—' }}</strong></div>
                        <div>{{ __('app.patients.limit_phosphorus') }}: <strong>{{ $patient->limit_phosphorus_mg_per_day ?? '—' }}</strong></div>
                        <div>{{ __('app.patients.limit_fluids') }}: <strong>{{ $patient->limit_fluids_ml_per_day ?? '—' }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('app.patients.menu_plans') }}</h3>
                        <a href="{{ route('menu-plans.index', ['patient_id' => $patient->id]) }}" class="text-emerald-600 hover:underline text-sm">
                            {{ __('app.dashboard.view_all') }}
                        </a>
                    </div>
                    <div class="space-y-2">
                        @forelse($menuPlans as $plan)
                            <a href="{{ route('menu-plans.show', $plan) }}" class="block text-sm text-emerald-600 hover:underline">
                                {{ $plan->name }}
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">—</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('app.patients.recipes') }}</h3>
                        <a href="{{ route('recipes.index', ['patient_id' => $patient->id]) }}" class="text-emerald-600 hover:underline text-sm">
                            {{ __('app.dashboard.view_all') }}
                        </a>
                    </div>
                    <div class="space-y-2">
                        @forelse($recipes as $recipe)
                            <a href="{{ route('recipes.show', $recipe) }}" class="block text-sm text-emerald-600 hover:underline">
                                {{ $recipe->name }}
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">—</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($patient->notes)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('app.common.notes') }}</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $patient->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
