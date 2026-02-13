<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('meal-types.index')">{{ __('app.meal_types.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $mealType->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editeaza: {{ $mealType->name }}
            @if($mealType->is_default)
                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                    Default
                </span>
            @endif
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('meal-types.update', $mealType) }}">
                        @csrf
                        @method('PUT')
                        @include('meal-types._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
