@props([
    'type' => 'info',
    'message' => null,
    'title' => null,
])

@php
    $styles = [
        'success' => 'border-success-500 bg-success-50 text-success-700 dark:border-success-500/30 dark:bg-success-500/15 dark:text-success-400',
        'error' => 'border-error-500 bg-error-50 text-error-700 dark:border-error-500/30 dark:bg-error-500/15 dark:text-error-400',
        'warning' => 'border-warning-500 bg-warning-50 text-warning-700 dark:border-warning-500/30 dark:bg-warning-500/15 dark:text-warning-300',
        'info' => 'border-brand-500 bg-brand-50 text-brand-700 dark:border-brand-500/30 dark:bg-brand-500/15 dark:text-brand-300',
    ];

    $containerClass = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->class(['w-full rounded-xl border p-4', $containerClass]) }}>
    @if ($title)
        <h4 class="mb-1 text-sm font-semibold">{{ $title }}</h4>
    @endif

    @if ($message)
        <p class="text-sm">{{ $message }}</p>
    @endif

    {{ $slot }}
</div>
