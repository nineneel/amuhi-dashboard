@props([
    'type' => 'neutral',
    'text' => null,
])

@php
    $styles = [
        'success' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
        'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-300',
        'danger' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
        'info' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
        'neutral' => 'bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-gray-300',
    ];

    $styleClass = $styles[$type] ?? $styles['neutral'];
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', $styleClass]) }}>
    {{ $text ?? $slot }}
</span>
