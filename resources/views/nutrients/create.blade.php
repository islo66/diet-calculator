<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add nutrient row</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('nutrients.store') }}" class="space-y-4">
                        @csrf
                        @include('nutrients._form', ['nutrient' => null, 'foods' => $foods, 'foodId' => $foodId])

                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Save
                            </button>
                            <a href="{{ route('nutrients.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                                Cancel
                            </a>
                        </div>
                    </form>

                    @if ($errors->any())
                        <div class="mt-4 text-sm text-red-600">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
