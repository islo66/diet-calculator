<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('meal-types.index')">Tipuri de Mese</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">Nou</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Adauga Tip de Masa</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('meal-types.store') }}">
                        @csrf
                        @include('meal-types._form', ['mealType' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>