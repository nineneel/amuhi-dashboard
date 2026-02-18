@props(['paginator'])

@php
    $perPageOptions = [10, 15, 25, 50];
    $selectedPerPage = (int) request('per_page', (int) $paginator->perPage());
@endphp

<div {{ $attributes->class(['border-t border-gray-200 px-6 py-4 dark:border-white/[0.05]']) }}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ url()->current() }}" class="flex items-center">
            @foreach (request()->except(['page', 'per_page']) as $queryKey => $queryValue)
                @if (is_array($queryValue))
                    @foreach ($queryValue as $nestedValue)
                        <input type="hidden" name="{{ $queryKey }}[]" value="{{ $nestedValue }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                @endif
            @endforeach

            <div class="relative">
                <select id="table_per_page" name="per_page"
                    aria-label="{{ __('ui.admin.rows') }}"
                    class="h-10 appearance-none rounded-lg border border-gray-200 bg-transparent px-4 pr-10 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-white/[0.03] dark:text-white/90 dark:focus:border-brand-800"
                    onchange="this.form.submit()">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($selectedPerPage === $option)>
                            {{ $option }} {{ __('ui.admin.rows') }}
                        </option>
                    @endforeach
                </select>

                <span class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
        </form>

        @if ($paginator->hasPages())
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $startPage = max(1, $currentPage - 1);
                $endPage = min($lastPage, $currentPage + 1);

                $displayedPages = [];

                for ($page = 1; $page <= $lastPage; $page++) {
                    if (
                        $page === 1 ||
                        $page === $lastPage ||
                        ($page >= $startPage && $page <= $endPage)
                    ) {
                        $displayedPages[] = $page;
                    } elseif (end($displayedPages) !== '...') {
                        $displayedPages[] = '...';
                    }
                }
            @endphp

            <div class="flex w-full items-center justify-between gap-3 sm:w-auto">
                @if ($paginator->onFirstPage())
                    <span
                        class="flex cursor-not-allowed items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-400 shadow-theme-xs dark:border-gray-700 dark:bg-gray-800 dark:text-gray-600 sm:px-3.5">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
                                fill="currentColor" />
                        </svg>
                        <span class="hidden sm:inline">{{ __('ui.admin.previous') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
                                fill="currentColor" />
                        </svg>
                        <span class="hidden sm:inline">{{ __('ui.admin.previous') }}</span>
                    </a>
                @endif

                <span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">
                    {{ __('ui.admin.page_current_of_last', ['current' => $currentPage, 'last' => $lastPage]) }}
                </span>

                <ul class="hidden items-center gap-0.5 sm:flex">
                    @foreach ($displayedPages as $page)
                        <li>
                            @if ($page === '...')
                                <span class="flex h-10 w-10 items-center justify-center text-gray-500">...</span>
                            @elseif ($page === $currentPage)
                                <span
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500 text-sm font-medium text-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $paginator->url($page) }}"
                                    class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-blue-500/[0.08] hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5">
                        <span class="hidden sm:inline">{{ __('ui.admin.next') }}</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                @else
                    <span
                        class="flex cursor-not-allowed items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-sm font-medium text-gray-400 shadow-theme-xs dark:border-gray-700 dark:bg-gray-800 dark:text-gray-600 sm:px-3.5">
                        <span class="hidden sm:inline">{{ __('ui.admin.next') }}</span>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>
