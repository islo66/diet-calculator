<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :active="true">{{ __('app.meal_types.title') }}</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('app.meal_types.title') }}</h2>
            <a href="{{ route('meal-types.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                {{ __('app.meal_types.add_new') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.meal_types.order') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.table.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.common.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.meal_types.type') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('app.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($mealTypes as $mealType)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $mealType->default_sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $mealType->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($mealType->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ __('app.common.active') }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ __('app.common.inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($mealType->is_default)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ __('app.meal_types.default') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">{{ __('app.meal_types.custom') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('meal-types.edit', $mealType) }}" class="text-emerald-600 hover:text-emerald-900 mr-3">
                                    {{ __('app.common.edit') }}
                                </a>
                                @unless($mealType->is_default)
                                    <form method="POST" action="{{ route('meal-types.destroy', $mealType) }}" class="inline" onsubmit="return confirm('{{ __('app.meal_types.delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('app.common.delete') }}</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                {{ __('app.meal_types.empty') }} <a href="{{ route('meal-types.create') }}" class="text-emerald-600 hover:underline">{{ __('app.meal_types.add_first') }}</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                @if($mealTypes->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $mealTypes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
