<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('recipes.index')">Retete</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :href="route('recipes.show', $recipeItem->recipe)">{{ $recipeItem->recipe->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $recipeItem->food?->name ?? 'Ingredient' }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editeaza Ingredient: {{ $recipeItem->food?->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('recipe-items.update', $recipeItem) }}">
                        @csrf
                        @method('PUT')
                        @include('recipe-items._form', ['recipe' => $recipeItem->recipe])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>