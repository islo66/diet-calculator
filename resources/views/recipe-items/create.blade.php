<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('recipes.index')">{{ __('app.recipes.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :href="route('recipes.show', $recipe)">{{ $recipe->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ __('app.recipe_items.new') }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.recipe_items.add_in', ['name' => $recipe->name]) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('recipe-items.store', $recipe) }}">
                        @csrf
                        @include('recipe-items._form', ['recipeItem' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
