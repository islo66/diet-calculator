<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :active="true">{{ __('app.patients.title') }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('app.patients.title') }}</h2>
            <a
                href="{{ route('patients.create') }}"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700"
            >
                {{ __('app.patients.add') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('patients.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                            <div class="md:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.search') }}</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="{{ __('app.patients.search_placeholder') }}"
                                >
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.common.status') }}</label>
                                <select
                                    name="is_active"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="">{{ __('app.common.all') }}</option>
                                    <option value="1" {{ $isActive === '1' ? 'selected' : '' }}>{{ __('app.common.active') }}</option>
                                    <option value="0" {{ $isActive === '0' ? 'selected' : '' }}>{{ __('app.common.inactive') }}</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.table.per_page') }}</label>
                                <select
                                    name="per_page"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
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
                                        href="{{ route('patients.index') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 w-full md:w-auto"
                                    >
                                        {{ __('app.common.reset') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.table.id') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.table.name') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.patients.sex') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.patients.birthdate') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.patients.diagnosis') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.patients.menu_plans') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.patients.recipes') }}</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">{{ __('app.common.status') }}</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">{{ __('app.common.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($patients as $patient)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b">{{ $patient->id }}</td>
                                    <td class="px-4 py-3 border-b">
                                        <a href="{{ route('patients.show', $patient) }}" class="text-emerald-600 hover:underline font-medium">
                                            {{ $patient->full_name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 border-b">
                                        @if($patient->sex === 'M')
                                            {{ __('app.patients.sex_m') }}
                                        @elseif($patient->sex === 'F')
                                            {{ __('app.patients.sex_f') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b">{{ $patient->birthdate?->format('d.m.Y') ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $patient->diagnosis ?: '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $patient->menu_plans_count }}</td>
                                    <td class="px-4 py-3 border-b">{{ $patient->recipes_count }}</td>
                                    <td class="px-4 py-3 border-b">
                                        @if($patient->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ __('app.common.active') }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ __('app.common.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b">
                                        <div class="flex items-center gap-3 justify-end whitespace-nowrap">
                                            <a href="{{ route('patients.show', $patient) }}" class="text-emerald-600 hover:underline">{{ __('app.common.view') }}</a>
                                            <a href="{{ route('patients.edit', $patient) }}" class="text-emerald-600 hover:underline">{{ __('app.common.edit') }}</a>
                                            <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('{{ __('app.patients.delete_confirm') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">{{ __('app.common.delete') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-8 border-b text-gray-500 text-center" colspan="9">
                                        {{ __('app.patients.empty') }}
                                        <a href="{{ route('patients.create') }}" class="text-emerald-600 hover:underline">{{ __('app.patients.add_first') }}</a>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($patients->hasPages())
                        <div class="mt-4">
                            {{ $patients->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
