<aside class="w-64 bg-gray-900 min-h-screen flex flex-col">
    {{-- Logo / Brand --}}
    <div class="p-4 border-b border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
            <div>
                <div class="font-bold text-white text-lg">Diet Calculator</div>
                <div class="text-gray-400 text-xs">Planificator Nutritional</div>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-4 space-y-6 overflow-y-auto">
        {{-- Dashboard --}}
        <div>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                      {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Dashboard</span>
            </a>
        </div>

        {{-- Planificare --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Planificare
            </div>
            <div class="space-y-1">
                <a href="{{ route('menu-plans.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('menu-plans.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Planuri Meniu</span>
                </a>

                <a href="{{ route('menu-plans.create') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm">Plan Nou</span>
                </a>
            </div>
        </div>

        {{-- Retete --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Retete
            </div>
            <div class="space-y-1">
                <a href="{{ route('recipes.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('recipes.*') || request()->routeIs('recipe-items.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Toate Retetele</span>
                </a>

                <a href="{{ route('recipes.create') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm">Reteta Noua</span>
                </a>
            </div>
        </div>

        {{-- Catalog --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Catalog Alimente
            </div>
            <div class="space-y-1">
                <a href="{{ route('foods.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('foods.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Alimente</span>
                </a>

                <a href="{{ route('nutrients.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors
                          {{ request()->routeIs('nutrients.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>Nutrienti</span>
                </a>

                <a href="{{ route('nutrients.create') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white ml-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="text-sm">Adauga Nutrient</span>
                </a>
            </div>
        </div>

        {{-- Management (placeholder pentru viitor) --}}
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Management
            </div>
            <div class="space-y-1">
                <a href="#"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors text-gray-500 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Pacienti</span>
                    <span class="ml-auto text-xs bg-gray-700 text-gray-400 px-2 py-0.5 rounded">Soon</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- User Info / Footer --}}
    @auth
    <div class="p-4 border-t border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gray-700 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-gray-300">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-1 text-gray-400 hover:text-white" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    @endauth
</aside>
