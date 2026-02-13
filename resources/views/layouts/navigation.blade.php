<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">
            {{-- Breadcrumbs --}}
            <div class="flex items-center">
                @isset($breadcrumbs)
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            {{ $breadcrumbs }}
                        </ol>
                    </nav>
                @else
                    <div class="text-sm text-gray-500">
                        {{ now()->format('l, d F Y') }}
                    </div>
                @endisset
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-4">
                {{-- Quick Actions --}}
                <a href="{{ route('menu-plans.create') }}"
                   class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('app.navigation.new_plan') }}
                </a>

                {{-- User Dropdown (mobile) --}}
                <div class="sm:hidden" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
