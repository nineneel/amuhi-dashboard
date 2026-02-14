@props([
    'title',
    'description',
    'icon' => null,
    'isComingSoon' => true,
])

<article
    {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/3']) }}
    data-testid="program-card"
>
    <div class="relative p-5 pb-9">
        <div class="mb-5 inline-flex h-24 w-18 items-center justify-center">
            @if (! empty($icon))
                <img
                    src="{{ asset($icon) }}"
                    alt="{{ $title }}"
                    class="h-24 w-24 object-cover"
                    loading="lazy"
                />
            @endif
        </div>

        <div class="flex items-start justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ $title }}
            </h3>
        </div>

        <p class="mt-3 max-w-xs text-sm text-gray-500 dark:text-gray-400">
            {{ $description }}
        </p>
    </div>

    <div class="border-t border-gray-200 p-5 dark:border-gray-800">
        <x-ui.button variant="outline" :disabled="true">
            Detail
        </x-ui.button>
    </div>
</article>
