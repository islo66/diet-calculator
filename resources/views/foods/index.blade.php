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
                            @foreach ($foods as $food)
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
                            @endforeach
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
