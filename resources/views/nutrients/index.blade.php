<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nutrients</h2>
            <a
                href="{{ route('nutrients.create', array_filter(['food_id' => $foodId])) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
                Add
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="GET" action="{{ route('nutrients.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search (food name)</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Search by food..."
                                >
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Food</label>
                                <select name="food_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All</option>
                                    @foreach ($foods as $f)
                                        <option value="{{ $f->id }}" {{ (string)$foodId === (string)$f->id ? 'selected' : '' }}>
                                            {{ $f->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Per page</label>
                                <select name="per_page" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ([10,25,50,100] as $pp)
                                        <option value="{{ $pp }}" {{ (int)$perPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
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
                                        href="{{ route('nutrients.index') }}"
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">ID</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Food</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Basis</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Kcal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Protein</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Fat</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Carb</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Na</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">K</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">P</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($nutrients as $n)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b">{{ $n->id }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->food?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->basis_qty }} {{ $n->basis_unit }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->kcal ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->protein_g ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->fat_g ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->carb_g ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->sodium_mg ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->potassium_mg ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">{{ $n->phosphorus_mg ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">
                                        <div class="flex items-center gap-3 justify-end">
                                            <a href="{{ route('nutrients.edit', $n) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('nutrients.destroy', $n) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3 border-b text-gray-500" colspan="11">No results.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $nutrients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
