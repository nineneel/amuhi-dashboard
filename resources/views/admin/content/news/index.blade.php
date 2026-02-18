@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.news') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.manage_news_articles_and_publication_status') }}</p>
            </div>
            <a href="{{ route('admin.news.create') }}"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                Add News
            </a>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.news.index'),
                'searchPlaceholder' => 'Search by title or author',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => collect($statuses)->mapWithKeys(fn ($status) => [$status->value => ucfirst($status->value)])->toArray(),
                    ],
                    [
                        'name' => 'category',
                        'label' => 'Category',
                        'options' => $categories->mapWithKeys(fn ($category) => [$category => $category])->toArray(),
                    ],
                    [
                        'name' => 'per_page',
                        'label' => 'Rows',
                        'include_all_option' => false,
                        'options' => [
                            '10' => '10 rows',
                            '15' => '15 rows',
                            '25' => '25 rows',
                            '50' => '50 rows',
                        ],
                        'value' => (string) request('per_page', '15'),
                    ],
                ],
                'class' => 'grid-cols-1 md:grid-cols-6',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Article'],
                ['label' => 'Category'],
                ['label' => 'Status'],
                ['label' => 'Published'],
                ['label' => 'Actions'],
            ],
            'paginator' => $news,
        ])
            @forelse ($news as $article)
                @php
                    $statusValue = is_object($article->status) ? $article->status->value : (string) $article->status;
                    $statusBadge = match ($statusValue) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'neutral',
                        default => 'neutral',
                    };
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $article->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $article->author_name }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $article->category }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $statusBadge, 'text' => ucfirst($statusValue)])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $article->published_at?->format('d M Y') ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex items-center gap-1">
                            @include('admin.components.table-action-icons', [
                                'viewUrl' => route('admin.news.show', $article),
                                'editUrl' => route('admin.news.edit', $article),
                                'deleteUrl' => route('admin.news.destroy', $article),
                                'deleteConfirm' => 'Delete this article?',
                            ])

                            @if ($statusValue !== 'published')
                                <form method="POST" action="{{ route('admin.news.publish', $article) }}">
                                    @csrf
                                    <button type="submit" aria-label="{{ __('ui.admin.publish') }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-success-50 hover:text-success-600 dark:text-gray-400 dark:hover:bg-success-500/10 dark:hover:text-success-300">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 2.5V17.5M10 2.5L14.1667 6.66667M10 2.5L5.83333 6.66667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.news.unpublish', $article) }}">
                                    @csrf
                                    <button type="submit" aria-label="{{ __('ui.admin.unpublish') }}"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-warning-50 hover:text-warning-600 dark:text-gray-400 dark:hover:bg-warning-500/10 dark:hover:text-warning-300">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 17.5V2.5M10 17.5L14.1667 13.3333M10 17.5L5.83333 13.3333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_news_articles_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
