<x-app-layout>
    <x-slot name="breadcrumbs">
        <x-breadcrumb :active="true">Planuri Meniu</x-breadcrumb>
    </x-slot>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Planuri Meniu</h2>
            <a
                href="{{ route('menu-plans.create', array_filter(['patient_id' => $patientId])) }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
            >
                Adauga Plan
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cauta (nume plan)</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $q }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Cauta dupa nume..."
                                >
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pacient</label>
                                <select name="patient_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toti</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}" {{ (string)$patientId === (string)$p->id ? 'selected' : '' }}>
                                            {{ $p->last_name }} {{ $p->first_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Per pagina</label>
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
                                        Aplica
                                    </button>
                                    <a
                                        href="{{ route('menu-plans.index') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 w-full md:w-auto"
                                    >
                                        Reset
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
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">ID</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Nume Plan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Pacient</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Perioada</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Zile</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b">Activ</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700 border-b"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($menuPlans as $plan)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b">{{ $plan->id }}</td>
                                    <td class="px-4 py-3 border-b">
                                        <a href="{{ route('menu-plans.show', $plan) }}" class="text-indigo-600 hover:underline font-medium">
                                            {{ $plan->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 border-b">{{ $plan->patient?->full_name ?? '—' }}</td>
                                    <td class="px-4 py-3 border-b">
                                        @if($plan->starts_at && $plan->ends_at)
                                            {{ $plan->starts_at->format('d.m.Y') }} - {{ $plan->ends_at->format('d.m.Y') }}
                                        @elseif($plan->starts_at)
                                            Din {{ $plan->starts_at->format('d.m.Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b">{{ $plan->days->count() }}</td>
                                    <td class="px-4 py-3 border-b">
                                        @if($plan->is_active)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Da</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Nu</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 border-b">
                                        <div class="flex items-center gap-3 justify-end">
                                            <a href="{{ route('menu-plans.show', $plan) }}" class="text-indigo-600 hover:underline">Vezi</a>
                                            <a href="{{ route('menu-plans.edit', $plan) }}" class="text-indigo-600 hover:underline">Editeaza</a>
                                            <form method="POST" action="{{ route('menu-plans.destroy', $plan) }}" onsubmit="return confirm('Sigur vrei sa stergi acest plan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Sterge</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3 border-b text-gray-500" colspan="7">Niciun plan gasit.</td>
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