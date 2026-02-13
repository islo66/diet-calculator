@props(['href' => null, 'active' => false])

<li class="inline-flex items-center">
    @if($href && !$active)
        <a href="{{ $href }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-emerald-600">
            {{ $slot }}
        </a>
    @else
        <span class="text-sm font-medium {{ $active ? 'text-gray-900' : 'text-gray-500' }}">
            {{ $slot }}
        </span>
    @endif
</li>
