@props([
    'viewUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteConfirm' => 'Are you sure?',
    'deleteLabel' => 'Delete',
])

<div class="flex items-center gap-1">
    @if ($viewUrl)
        <a href="{{ $viewUrl }}" aria-label="View"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-brand-50 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-brand-500/10 dark:hover:text-brand-300">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.66699 10C2.75033 6.66667 5.25033 5 10.0003 5C14.7503 5 17.2503 6.66667 18.3337 10C17.2503 13.3333 14.7503 15 10.0003 15C5.25033 15 2.75033 13.3333 1.66699 10Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="10.0003" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5" />
            </svg>
        </a>
    @endif

    @if ($editUrl)
        <a href="{{ $editUrl }}" aria-label="Edit"
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-warning-50 hover:text-warning-600 dark:text-gray-400 dark:hover:bg-warning-500/10 dark:hover:text-warning-300">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.6663 3.33325L16.6663 8.33325" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M3.33301 16.6667L7.56634 15.726C8.07315 15.6134 8.53845 15.3631 8.91134 15.001L15.7158 8.39368C16.366 7.76228 16.3802 6.72263 15.7475 6.07243C15.7408 6.06558 15.7342 6.05879 15.7273 6.05208L13.9476 4.27235C13.2974 3.63966 12.2577 3.65391 11.6263 4.30408C11.6196 4.31097 11.6128 4.31762 11.606 4.32444L4.99865 11.1289C4.63658 11.5018 4.38634 11.9671 4.27367 12.4739L3.33301 16.6667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    @endif

    @if ($deleteUrl)
        <form method="POST" action="{{ $deleteUrl }}" onsubmit="return confirm('{{ $deleteConfirm }}')">
            @csrf
            @method('DELETE')
            <button type="submit" aria-label="{{ $deleteLabel }}"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-error-50 hover:text-error-600 dark:text-gray-400 dark:hover:bg-error-500/10 dark:hover:text-error-300">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.33301 2.5H11.6663M2.5 5H17.5M15.833 5L15.2482 14.3576C15.1869 15.3381 14.3739 16.1009 13.3915 16.1009H6.6082C5.6258 16.1009 4.8128 15.3381 4.75152 14.3576L4.16634 5M7.5 8.33333V12.5M12.5 8.33333V12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </form>
    @endif
</div>
