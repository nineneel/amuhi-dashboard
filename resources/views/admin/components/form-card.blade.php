@props([
    'title',
    'description' => null,
    'action' => null,
    'method' => 'POST',
])

@php
    $resolvedMethod = strtoupper((string) $method);
@endphp

<div {{ $attributes->class(['rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]']) }}>
    <div class="px-6 py-5">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __($title) }}</h3>

        @if ($description)
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __($description) }}</p>
        @endif
    </div>

    <div class="border-t border-gray-100 p-4 dark:border-gray-800 sm:p-6">
        @if ($action)
            <form action="{{ $action }}" method="{{ $resolvedMethod === 'GET' ? 'GET' : 'POST' }}" class="space-y-6">
                @if ($resolvedMethod !== 'GET')
                    @csrf
                @endif

                @if (! in_array($resolvedMethod, ['GET', 'POST'], true))
                    @method($resolvedMethod)
                @endif

                {{ $slot }}
            </form>
        @else
            <div class="space-y-6">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
