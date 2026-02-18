@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.news_preview') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $news->title }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.news.edit', $news) }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Edit Article
                </a>
                @php
                    $statusValue = is_object($news->status) ? $news->status->value : $news->status;
                @endphp
                @if ($statusValue !== 'published')
                    <form method="POST" action="{{ route('admin.news.publish', $news) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-success-300 bg-success-50 px-4 py-2.5 text-sm font-medium text-success-700 shadow-theme-xs transition hover:bg-success-100 dark:border-success-700 dark:bg-success-500/15 dark:text-success-400">
                            Publish
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.news.unpublish', $news) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-warning-300 bg-warning-50 px-4 py-2.5 text-sm font-medium text-warning-700 shadow-theme-xs transition hover:bg-warning-100 dark:border-warning-700 dark:bg-warning-500/15 dark:text-warning-400">
                            Unpublish
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.news.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Back to News
                </a>
            </div>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.article_info') }}</h2>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.status') }}</span>
                            <span class="w-3/5">
                                @php
                                    $statusBadge = match ($statusValue) {
                                        'published' => 'success',
                                        'draft' => 'warning',
                                        'archived' => 'neutral',
                                        default => 'neutral',
                                    };
                                @endphp
                                @include('admin.components.badge', ['type' => $statusBadge, 'text' => ucfirst($statusValue)])
                            </span>
                        </li>
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.category') }}</span>
                            <span class="w-3/5 text-sm text-gray-700 dark:text-gray-300">{{ $news->category }}</span>
                        </li>
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.author') }}</span>
                            <span class="w-3/5 text-sm text-gray-700 dark:text-gray-300">{{ $news->author_name }}</span>
                        </li>
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.read_time') }}</span>
                            <span class="w-3/5 text-sm text-gray-700 dark:text-gray-300">{{ $news->read_time_minutes }} min</span>
                        </li>
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.published') }}</span>
                            <span class="w-3/5 text-sm text-gray-700 dark:text-gray-300">
                                {{ $news->published_at?->format('d M Y') ?? '-' }}
                            </span>
                        </li>
                        @if ($news->badge)
                            <li class="flex items-start gap-3 py-2.5">
                                <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.badge') }}</span>
                                <span class="w-3/5 text-sm text-gray-700 dark:text-gray-300">{{ $news->badge }}</span>
                            </li>
                        @endif
                        @if (!empty($news->tags))
                            <li class="flex items-start gap-3 py-2.5">
                                <span class="w-2/5 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.tags') }}</span>
                                <span class="w-3/5 text-sm text-gray-700 dark:text-gray-300">
                                    {{ implode(', ', $news->tags) }}
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-2 text-xl font-bold text-gray-800 dark:text-white/90">{{ $news->title }}</h2>
                    <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">{{ $news->summary }}</p>

                    <div class="space-y-4">
                        @foreach ($news->content ?? [] as $block)
                            @if ($block['type'] === 'paragraph')
                                <p class="text-sm leading-relaxed text-gray-700 dark:text-gray-300">{{ $block['value'] }}</p>
                            @elseif ($block['type'] === 'heading')
                                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $block['value'] }}</h3>
                            @elseif ($block['type'] === 'list')
                                <ul class="list-inside list-disc space-y-1 text-sm text-gray-700 dark:text-gray-300">
                                    @foreach (explode("\n", $block['value']) as $item)
                                        @if (trim($item))
                                            <li>{{ trim($item) }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @elseif ($block['type'] === 'quote')
                                <blockquote class="border-l-4 border-brand-500 pl-4 italic text-gray-600 dark:text-gray-400">
                                    {{ $block['value'] }}
                                </blockquote>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
