<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :active="true">Alimente</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Alimente</h2>
            <a href="{{ route('foods.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Adauga Aliment
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
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('foods.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cauta</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Cauta dupa nume..."
                                >
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
                                <select
                                    name="category_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Toate</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pe pagina</label>
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

                            <div class="md:col-span-2">
                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                    >
                                        Filtreaza
                                    </button>
                                    <a
                                        href="{{ route('foods.index') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50"
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Nume</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Categorie</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 border-b">Unitate</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">Kcal</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">Prot</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">Gras</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">Carb</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">Na</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">K</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">P</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-700 border-b">Status</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 border-b">Actiuni</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($foods as $food)
                                @php($n = $food->nutrient)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b">
                                        <a href="{{ route('foods.show', $food) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $food->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 border-b text-gray-600">{{ $food->category?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-center">{{ $food->default_unit }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->kcal ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->protein_g ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->fat_g ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->carb_g ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->sodium_mg ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->potassium_mg ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-right">{{ $n?->phosphorus_mg ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b text-center">
                                        @if($food->is_active)
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activ</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactiv</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b text-right whitespace-nowrap">
                                        <a href="{{ route('foods.edit', $food) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-8 border-b text-gray-500 text-center" colspan="12">
                                        Niciun aliment gasit.
                                        <a href="{{ route('foods.create') }}" class="text-indigo-600 hover:underline">Adauga primul aliment.</a>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($foods->hasPages())
                        <div class="mt-4">
                            {{ $foods->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>