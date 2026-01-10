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
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($foods as $food)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 border-b">{{ $food->id }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->name }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->category }}</td>
                                    <td class="px-6 py-4 border-b">{{ $food->default_unit }}</td>
                                    <td class="px-6 py-4 border-b">
                                        {{ $food->is_active ? 'Yes' : 'No' }}
                                    </td>
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
