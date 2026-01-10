<aside class="w-64 bg-white border-r min-h-screen">
    <div class="p-4 border-b">
        <div class="font-semibold text-gray-800">Menu</div>
    </div>

    <nav class="p-2 space-y-1">
        <a href="{{ route('dashboard') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-100 font-semibold' : '' }}">
            Dashboard
        </a>

        <a href="{{ route('foods.index') }}"
           class="block px-3 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('foods.*') ? 'bg-gray-100 font-semibold' : '' }}">
            Foods
        </a>
    </nav>
</aside>
