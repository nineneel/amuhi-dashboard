@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ $news->title }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($news->status->value ?? $news->status) }}</p>
            </div>

            <a href="{{ route('admin.news.edit', $news) }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Edit</a>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $news->summary }}</p>

            <div class="mt-4 space-y-3">
                @foreach ($news->content ?? [] as $block)
                    @if (($block['type'] ?? '') === 'heading')
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $block['text'] ?? '' }}</h2>
                    @else
                        <p class="text-theme-sm text-gray-700 dark:text-gray-300">{{ $block['text'] ?? '' }}</p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
