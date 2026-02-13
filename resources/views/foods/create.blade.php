<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('foods.index')">{{ __('app.foods.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ __('app.common.new') }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('app.foods.add') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('foods.store') }}">
                        @csrf
                        @include('foods._form', ['food' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
