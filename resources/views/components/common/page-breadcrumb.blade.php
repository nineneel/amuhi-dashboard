@props(['pageTitle' => 'Page', 'items' => []])

<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        {{ $pageTitle }}
    </h2>

    @if (! empty($items))
        <x-ui.breadcrumb :items="$items" variant="default" class="mt-2" />
    @endif
</div>
