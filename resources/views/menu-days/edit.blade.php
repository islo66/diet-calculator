<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('menu-plans.index')">{{ __('app.menu_plans.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :href="route('menu-plans.show', $menuDay->menuPlan)">{{ $menuDay->menuPlan->name }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">{{ $menuDay->name }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('app.menu_days.edit') }}: {{ $menuDay->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('menu-days.update', $menuDay) }}">
                        @csrf
                        @method('PUT')
                        @include('menu-days._form', ['menuPlan' => $menuDay->menuPlan])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
