<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :active="true">Dashboard</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bine ai venit, {{ Auth::user()->name }}!
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Quick Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $menuPlansCount = \App\Models\MenuPlan::count();
                    $activePlansCount = \App\Models\MenuPlan::where('is_active', true)->count();
                    $foodsCount = \App\Models\Food::where('is_active', true)->count();
                    $patientsCount = \App\Models\Patient::where('is_active', true)->count();
                @endphp

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Planuri Meniu</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $menuPlansCount }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-green-600">{{ $activePlansCount }} active</span>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Alimente</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $foodsCount }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('foods.index') }}" class="text-sm text-indigo-600 hover:underline">Vezi toate</a>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pacienti</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $patientsCount }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-500">activi</span>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-sm p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-100">Actiune rapida</p>
                            <p class="text-lg font-semibold mt-1">Creeaza Plan Nou</p>
                        </div>
                        <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('menu-plans.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg text-sm font-medium hover:bg-indigo-50 transition-colors">
                            Incepe
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Plans --}}
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Planuri Recente</h3>
                        <a href="{{ route('menu-plans.index') }}" class="text-sm text-indigo-600 hover:underline">Vezi toate</a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    @php
                        $recentPlans = \App\Models\MenuPlan::with(['patient', 'days'])
                            ->orderByDesc('updated_at')
                            ->limit(5)
                            ->get();
                    @endphp

                    @forelse($recentPlans as $plan)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <a href="{{ route('menu-plans.show', $plan) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                        {{ $plan->name }}
                                    </a>
                                    <p class="text-sm text-gray-500">
                                        {{ $plan->patient?->full_name ?? 'Fara pacient' }}
                                        &middot;
                                        {{ $plan->days->count() }} zile
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    @if($plan->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activ
                                        </span>
                                    @endif
                                    <a href="{{ route('menu-plans.show', $plan) }}" class="text-indigo-600 hover:text-indigo-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>Niciun plan creat inca.</p>
                            <a href="{{ route('menu-plans.create') }}" class="mt-2 inline-block text-indigo-600 hover:underline">
                                Creeaza primul plan
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('menu-plans.create') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Plan Meniu Nou</h4>
                            <p class="text-sm text-gray-500">Creeaza un plan saptamanal</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('foods.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Cauta Alimente</h4>
                            <p class="text-sm text-gray-500">Exploreaza catalogul</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('nutrients.create') }}" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Adauga Nutrienti</h4>
                            <p class="text-sm text-gray-500">Completeaza valorile nutritionale</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
