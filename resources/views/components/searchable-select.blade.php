@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => __('app.common.search') . '...',
    'displayKey' => 'name',
    'valueKey' => 'id',
    'required' => false,
    'limit' => 20,
])

@php
    $selectedOption = collect($options)->firstWhere($valueKey, $value);
    $selectedText = $selectedOption ? $selectedOption[$displayKey] : '';
@endphp

<div
    x-data="{
        open: false,
        search: '',
        selectedValue: '{{ $value }}',
        selectedText: '{{ addslashes($selectedText) }}',
        options: {{ Js::from($options) }},
        displayKey: '{{ $displayKey }}',
        valueKey: '{{ $valueKey }}',
        limit: {{ $limit }},
        highlightedIndex: -1,

        get filteredOptions() {
            let filtered = this.options;

            if (this.search.length > 0) {
                const searchLower = this.search.toLowerCase();
                filtered = this.options.filter(opt => {
                    const text = (opt[this.displayKey] || '').toLowerCase();
                    return text.includes(searchLower);
                });
            }

            return filtered.slice(0, this.limit);
        },

        selectOption(option) {
            this.selectedValue = option[this.valueKey];
            this.selectedText = option[this.displayKey];
            this.search = '';
            this.open = false;
            this.highlightedIndex = -1;
            this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        },

        clearSelection() {
            this.selectedValue = '';
            this.selectedText = '';
            this.search = '';
            this.$refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        },

        handleKeydown(event) {
            if (!this.open) {
                if (event.key === 'ArrowDown' || event.key === 'Enter') {
                    this.open = true;
                    event.preventDefault();
                }
                return;
            }

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    this.highlightedIndex = Math.min(this.highlightedIndex + 1, this.filteredOptions.length - 1);
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    this.highlightedIndex = Math.max(this.highlightedIndex - 1, 0);
                    break;
                case 'Enter':
                    event.preventDefault();
                    if (this.highlightedIndex >= 0 && this.filteredOptions[this.highlightedIndex]) {
                        this.selectOption(this.filteredOptions[this.highlightedIndex]);
                    }
                    break;
                case 'Escape':
                    this.open = false;
                    this.highlightedIndex = -1;
                    break;
            }
        }
    }"
    @click.away="open = false"
    class="relative"
>
    {{-- Hidden input pentru form submission --}}
    <input
        type="hidden"
        name="{{ $name }}"
        x-ref="hiddenInput"
        :value="selectedValue"
        {{ $required ? 'required' : '' }}
    >

    {{-- Afișare selecție curentă sau input căutare --}}
    <div class="relative">
        <template x-if="selectedValue && !open">
            <div
                @click="open = true; $nextTick(() => $refs.searchInput.focus())"
                class="w-full rounded-md border-gray-300 shadow-sm bg-white px-3 py-2 pr-10 cursor-pointer border focus:border-emerald-500 focus:ring-emerald-500 flex items-center justify-between"
            >
                <span x-text="selectedText" class="truncate"></span>
                <button
                    type="button"
                    @click.stop="clearSelection()"
                    class="absolute right-2 text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </template>

        <template x-if="!selectedValue || open">
            <input
                type="text"
                x-ref="searchInput"
                x-model="search"
                @focus="open = true"
                @keydown="handleKeydown($event)"
                placeholder="{{ $placeholder }}"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                autocomplete="off"
            >
        </template>
    </div>

    {{-- Dropdown cu opțiuni --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-auto"
        style="display: none;"
    >
        <template x-if="filteredOptions.length === 0">
            <div class="px-3 py-2 text-gray-500 text-sm">
                <template x-if="search.length > 0">
                    <span>{{ __('app.search.no_results_prefix') }} "<span x-text="search"></span>"</span>
                </template>
                <template x-if="search.length === 0">
                    <span>{{ __('app.search.no_options') }}</span>
                </template>
            </div>
        </template>

        <template x-for="(option, index) in filteredOptions" :key="option[valueKey]">
            <div
                @click="selectOption(option)"
                @mouseenter="highlightedIndex = index"
                :class="{
                    'bg-emerald-600 text-white': highlightedIndex === index,
                    'text-gray-900 hover:bg-gray-100': highlightedIndex !== index
                }"
                class="px-3 py-2 cursor-pointer text-sm"
            >
                <span x-text="option[displayKey]"></span>
                <template x-if="option.subtitle">
                    <span class="ml-2 text-xs" :class="highlightedIndex === index ? 'text-emerald-200' : 'text-gray-500'" x-text="option.subtitle"></span>
                </template>
            </div>
        </template>

        <template x-if="filteredOptions.length >= limit && search.length === 0">
            <div class="px-3 py-2 text-xs text-gray-400 border-t">
                {{ __('app.search.limit_prefix', ['count' => $limit]) }}
            </div>
        </template>
    </div>
</div>
