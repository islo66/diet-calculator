<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('menu-plans.index')">{{ __('app.menu_plans.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :href="route('menu-plans.show', $menuPlan)">{{ $menuPlan->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ __('app.menu_days.new') }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.menu_days.add_in', ['name' => $menuPlan->name]) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('menu-days.store', $menuPlan) }}">
                        @csrf
                        @include('menu-days._form', ['menuDay' => null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
