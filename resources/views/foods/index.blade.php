<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Foods
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="GET" action="{{ route('foods.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                            <div class="md:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Search by name..."
                                >
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <select
                                    name="category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Per page</label>
                                <select
                                    name="per_page"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    @foreach ([10,25,50,100] as $pp)
                                        <option value="{{ $pp }}" {{ (int)$perPage === $pp ? 'selected' : '' }}>
                                            {{ $pp }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <div class="flex gap-2 md:justify-end">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 w-full md:w-auto"
                                    >
                                        Apply
                                    </button>

                                    <a
                                        href="{{ route('foods.index') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 w-full md:w-auto"
                                    >
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">ID</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Name</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Category</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Unit</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Active</th>

                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Kcal</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Protein (g)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Fat (g)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Carbs (g)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Sodium (mg)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Potassium (mg)</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-700 border-b">Phosphorus (mg)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($foods as $food)
                                @php($n = $food->nutrient)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 border-b">{{ $food->id }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->name }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->category?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->default_unit }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->is_active ? 'Yes' : 'No' }}</td>

                                    <td class="px-6 py-4 border-b">{{ $n?->kcal ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $n?->protein_g ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $n?->fat_g ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $n?->carb_g ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $n?->sodium_mg ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $n?->potassium_mg ?? '—' }}</td>
                                    <td class="px-6 py-4 border-b">{{ $n?->phosphorus_mg ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-4 border-b text-gray-500" colspan="12">No results.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $foods->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
