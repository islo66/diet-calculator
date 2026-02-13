<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :href="route('foods.index')">{{ __('app.foods.title') }}</x-breadcrumb>
        <x-breadcrumb-separator />
        <x-breadcrumb :active="true">Merge duplicate</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Merge duplicate alimente</h2>
            <a href="{{ route('foods.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                Inapoi la alimente
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($groups === [])
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-gray-600">
                    Nu exista duplicate pe baza nume + categorie.
                </div>
            @endif

            @foreach($groups as $group)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $group['label'] }}</h3>
                        <p class="text-sm text-gray-500">Categorie: {{ $group['category'] ?? 'Fara categorie' }}</p>
                    </div>

                    <form method="POST" action="{{ route('foods.duplicates.merge') }}">
                        @csrf

                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse text-sm">
                                <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Pastrat</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">ID</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Nume</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Nutrienti</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Recipe items</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Meal items</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group['foods'] as $index => $food)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 border-b">
                                            <input
                                                type="radio"
                                                name="keep_food_id"
                                                value="{{ $food->id }}"
                                                {{ $index === 0 ? 'checked' : '' }}
                                                required
                                            >
                                        </td>
                                        <td class="px-4 py-3 border-b">{{ $food->id }}</td>
                                        <td class="px-4 py-3 border-b">{{ $food->name }}</td>
                                        <td class="px-4 py-3 border-b">{{ $food->nutrients_count }}</td>
                                        <td class="px-4 py-3 border-b">{{ $food->recipe_items_count }}</td>
                                        <td class="px-4 py-3 border-b">{{ $food->meal_items_count }}</td>
                                        <td class="px-4 py-3 border-b">
                                            @if($food->is_active)
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ __('app.common.active') }}</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ __('app.common.inactive') }}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <input type="hidden" name="food_ids[]" value="{{ $food->id }}">
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Conflict nutrienti</label>
                                <select
                                    name="nutrient_conflict"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="keep_target">Pastreaza valorile de pe alimentul pastrat</option>
                                    <option value="overwrite_target">Suprascrie cu valorile de pe duplicate</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700"
                                    onclick="return confirm('Sigur vrei sa faci merge pentru acest grup?')"
                                >
                                    Merge grup
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
