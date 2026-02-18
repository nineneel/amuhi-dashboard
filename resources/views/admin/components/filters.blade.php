@props([
    'filters' => [],
    'action' => null,
    'searchName' => 'search',
    'searchPlaceholder' => 'Search...',
    'showDateRange' => true,
])

<form action="{{ $action ?? url()->current() }}" method="GET"
    {{ $attributes->class(['grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4']) }}>
    <div class="xl:col-span-2">
        <label for="{{ $searchName }}" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
        <input id="{{ $searchName }}" name="{{ $searchName }}" type="search"
            value="{{ (string) request($searchName, '') }}" placeholder="{{ $searchPlaceholder }}"
            class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
    </div>

    @foreach ($filters as $filter)
        @php
            $name = $filter['name'] ?? null;
            $label = $filter['label'] ?? null;
            $options = $filter['options'] ?? [];
            $selectedValue = (string) request($name, $filter['value'] ?? '');
        @endphp

        @if ($name && $label)
            <div>
                <label for="{{ $name }}" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ $label }}
                </label>
                @php
                    $includeAllOption = (bool) ($filter['include_all_option'] ?? true);
                    $allLabel = (string) ($filter['all_label'] ?? "All {$label}");
                @endphp

                <div class="relative">
                    <select id="{{ $name }}" name="{{ $name }}"
                        class="h-11 w-full appearance-none rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-800">
                        @if ($includeAllOption)
                            <option value="">{{ $allLabel }}</option>
                        @endif

                        @foreach ($options as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected($selectedValue === (string) $optionValue)>
                                {{ $optionLabel }}
                            </option>
                        @endforeach
                    </select>

                    <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
        @endif
    @endforeach

    @if ($showDateRange)
        <div>
            <label for="date_from" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
            <input id="date_from" name="date_from" type="date" value="{{ (string) request('date_from', '') }}"
                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-800" />
        </div>

        <div>
            <label for="date_to" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
            <input id="date_to" name="date_to" type="date" value="{{ (string) request('date_to', '') }}"
                class="h-11 w-full rounded-lg border border-gray-200 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-800" />
        </div>
    @endif

    <div class="flex items-end gap-3 xl:col-span-4">
        <button type="submit"
            class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
            Apply Filters
        </button>

        <a href="{{ $action ?? url()->current() }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
            Reset
        </a>
    </div>
</form>
