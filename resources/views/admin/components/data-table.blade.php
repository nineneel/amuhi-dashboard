@props([
    'headers' => [],
    'sortable' => false,
    'sortField' => null,
    'sortDirection' => 'asc',
])

@php
    $resolvedSortField = $sortField ?? request('sort');
    $resolvedSortDirection = strtolower((string) $sortDirection) === 'desc' ? 'desc' : 'asc';
@endphp

<div {{ $attributes->class(['overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]']) }}>
    <div class="custom-scrollbar max-w-full overflow-x-auto">
        <table class="w-full min-w-[720px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    @foreach ($headers as $header)
                        @php
                            $label = is_array($header) ? ($header['label'] ?? '') : (string) $header;
                            $key = is_array($header) ? ($header['key'] ?? null) : null;
                            $canSort = (bool) ($sortable && $key && (is_array($header) ? ($header['sortable'] ?? true) : true));

                            $isActiveSort = $canSort && $resolvedSortField === $key;
                            $nextDirection = $isActiveSort && $resolvedSortDirection === 'asc' ? 'desc' : 'asc';
                            $sortUrl = $canSort ? request()->fullUrlWithQuery(['sort' => $key, 'direction' => $nextDirection]) : null;
                        @endphp

                        <th class="px-5 py-3 text-left sm:px-6">
                            @if ($canSort)
                                <a href="{{ $sortUrl }}"
                                    class="inline-flex items-center gap-2 text-theme-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <span>{{ $label }}</span>
                                    <svg class="h-4 w-4 {{ $isActiveSort ? 'text-brand-500 dark:text-brand-400' : 'text-gray-400 dark:text-gray-500' }}"
                                        viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        @if ($isActiveSort && $resolvedSortDirection === 'desc')
                                            <path d="M10 14.5L5 9.5H15L10 14.5Z" fill="currentColor" />
                                        @else
                                            <path d="M10 5.5L15 10.5H5L10 5.5Z" fill="currentColor" />
                                        @endif
                                    </svg>
                                </a>
                            @else
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
