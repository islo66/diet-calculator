<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('recipes.index')">Retete</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">Reteta Noua</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Adauga Reteta</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('recipes.store') }}">
                        @csrf
                        @include('recipes._form', ['recipe' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>