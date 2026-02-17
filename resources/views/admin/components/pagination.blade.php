@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
    @endphp

    <div {{ $attributes->class(['flex flex-wrap items-center justify-between gap-3 border-t border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6']) }}>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </p>

        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-600">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                    Previous
                </a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @if ($startPage > 1)
                    <a href="{{ $paginator->url(1) }}"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                        1
                    </a>

                    @if ($startPage > 2)
                        <span class="px-1 text-sm text-gray-500 dark:text-gray-400">...</span>
                    @endif
                @endif

                @for ($page = $startPage; $page <= $endPage; $page++)
                    @if ($page === $currentPage)
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                @if ($endPage < $lastPage)
                    @if ($endPage < $lastPage - 1)
                        <span class="px-1 text-sm text-gray-500 dark:text-gray-400">...</span>
                    @endif

                    <a href="{{ $paginator->url($lastPage) }}"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                        {{ $lastPage }}
                    </a>
                @endif
            </div>

            <span class="text-sm text-gray-600 dark:text-gray-400 sm:hidden">
                Page {{ $currentPage }} / {{ $lastPage }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                    Next
                </a>
            @else
                <span
                    class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-600">
                    Next
                </span>
            @endif
        </div>
    </div>
@endif
