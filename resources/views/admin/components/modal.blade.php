@props([
    'id',
    'title' => null,
    'size' => 'md',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-md',
        'lg' => 'max-w-3xl',
        'xl' => 'max-w-5xl',
        default => 'max-w-2xl',
    };
@endphp

<div x-data="{ open: false }" @open-modal-{{ $id }}.window="open = true"
    @close-modal-{{ $id }}.window="open = false" x-show="open" x-cloak
    class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5" @keydown.escape.window="open = false"
    role="dialog" aria-modal="true" aria-labelledby="modal-title-{{ $id }}">
    <div @click="open = false" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    <div @click.stop
        class="relative w-full {{ $sizeClass }} rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-8"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
        <button @click="open = false"
            class="absolute right-3 top-3 z-999 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        @if ($title)
            <h3 id="modal-title-{{ $id }}" class="mb-4 text-title-sm font-semibold text-gray-800 dark:text-white/90">
                {{ __($title) }}
            </h3>
        @endif

        <div class="space-y-4 text-sm text-gray-500 dark:text-gray-400">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="mt-6 flex items-center justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
