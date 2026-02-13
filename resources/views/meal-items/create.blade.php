<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('menu-plans.index')">{{ __('app.menu_plans.breadcrumb') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :href="route('menu-plans.show', $menuMeal->menuDay->menuPlan)">{{ $menuMeal->menuDay->menuPlan->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb>{{ $menuMeal->menuDay->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $menuMeal->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.meal_items.add_in', ['name' => $menuMeal->name]) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('meal-items.store', $menuMeal) }}">
                        @csrf
                        @include('meal-items._form', ['mealItem' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
