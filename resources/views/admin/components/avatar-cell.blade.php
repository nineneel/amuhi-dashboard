@props([
    'name',
    'subtitle' => null,
    'size' => 'md',
])

@php
    $resolvedName = trim((string) $name);
    $words = preg_split('/\s+/', $resolvedName) ?: [];

    $initials = collect($words)
        ->filter()
        ->take(2)
        ->map(fn (string $word) => strtoupper(substr($word, 0, 1)))
        ->implode('');

    if ($initials === '') {
        $initials = 'NA';
    }

    $sizeClass = $size === 'sm' ? 'h-8 w-8 text-xs' : 'h-10 w-10 text-sm';
@endphp

<div class="flex items-center gap-3">
    <div
        class="{{ $sizeClass }} inline-flex shrink-0 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">
        {{ $initials }}
    </div>

    <div class="min-w-0">
        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">{{ $resolvedName }}</p>
        @if ($subtitle)
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
        @endif
    </div>
</div>
