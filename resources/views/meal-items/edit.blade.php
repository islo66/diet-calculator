<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('menu-plans.index')">Planuri</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :href="route('menu-plans.show', $mealItem->menuMeal->menuDay->menuPlan)">{{ $mealItem->menuMeal->menuDay->menuPlan->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb>{{ $mealItem->menuMeal->menuDay->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $mealItem->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editeaza: {{ $mealItem->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('meal-items.update', $mealItem) }}">
                        @csrf
                        @method('PUT')
                        @include('meal-items._form', ['menuMeal' => $mealItem->menuMeal])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>