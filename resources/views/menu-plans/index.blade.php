<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :active="true">{{ __('app.menu_plans.title') }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('app.menu_plans.title') }}</h2>
            <a
                href="{{ route('menu-plans.create', array_filter(['patient_id' => $patientId])) }}"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700"
            >
                {{ __('app.menu_plans.add') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="GET" action="{{ route('menu-plans.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_plans.search_name') }}</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="{{ __('app.menu_plans.search_placeholder') }}"
                                >
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.menu_plans.patient') }}</label>
                                <select name="patient_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">{{ __('app.common.all') }}</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}" {{ (string)$patientId === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->last_name }} {{ $p->first_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.table.per_page') }}</label>
                                <select name="per_page" class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach ([10,25,50,100] as $pp)
                                        <option value="{{ $pp }}" {{ (int)$perPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <div class="flex gap-2 md:justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 w-full md:w-auto"
                                    >
                                        {{ __('app.common.apply') }}
                                    </button>
                                    <a
                                        href="{{ route('menu-plans.index') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 w-full md:w-auto"
                                    >
                                        {{ __('app.common.reset') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.table.id') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.menu_plans.name') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.menu_plans.patient') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.menu_plans.period') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.menu_plans.days') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.common.active') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($menuPlans as $plan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b">{{ $plan->id }}</td>
                                    <td class="px-4 py-3 border-b">
                                        <a href="{{ route('menu-plans.show', $plan) }}" class="text-emerald-600 hover:underline font-medium">
                                            {{ $plan->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 border-b">{{ $plan->patient?->full_name ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">
                                        @if($plan->starts_at && $plan->ends_at)
                                            {{ $plan->starts_at->format('d.m.Y') }} - {{ $plan->ends_at->format('d.m.Y') }}
                                        @elseif($plan->starts_at)
                                            {{ __('app.menu_plans.from_date', ['date' => $plan->starts_at->format('d.m.Y')]) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b">{{ $plan->days->count() }}</td>
                                    <td class="px-4 py-3 border-b">
                                        @if($plan->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('app.common.yes') }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ __('app.common.no') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b">
                                        <div class="flex items-center gap-3 justify-end">
                                            <a href="{{ route('menu-plans.show', $plan) }}" class="text-emerald-600 hover:underline">{{ __('app.common.view') }}</a>
                                            <a href="{{ route('menu-plans.edit', $plan) }}" class="text-emerald-600 hover:underline">{{ __('app.common.edit') }}</a>
                                            <form method="POST" action="{{ route('menu-plans.destroy', $plan) }}" onsubmit="return confirm('{{ __('app.menu_plans.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">{{ __('app.common.delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3 border-b text-gray-500" colspan="7">{{ __('app.menu_plans.empty') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $menuPlans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
