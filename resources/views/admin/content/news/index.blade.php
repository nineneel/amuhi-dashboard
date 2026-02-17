@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">News</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage CMS news articles.</p>
            </div>

            <a href="{{ route('admin.news.create') }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Create
                News</a>
        </div>

        @if (session('status'))
            @include('admin.components.alert', ['type' => 'success', 'title' => 'Success', 'message' => session('status')])
        @endif

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Title', 'key' => 'title'],
                ['label' => 'Category', 'key' => 'category'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Published At', 'key' => 'published_at'],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
        ])
            @forelse ($newsItems as $news)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $news->title }}</p>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $news->slug }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">{{ $news->category }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ ucfirst($news->status->value ?? $news->status) }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $news->published_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.news.show', $news) }}"
                                class="rounded-lg bg-brand-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-brand-600">View</a>
                            <a href="{{ route('admin.news.edit', $news) }}"
                                class="rounded-lg bg-gray-700 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-gray-600">Edit</a>

                            @if (($news->status->value ?? $news->status) === 'published')
                                <form action="{{ route('admin.news.unpublish', $news) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-warning-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-warning-600">Unpublish</button>
                                </form>
                            @else
                                <form action="{{ route('admin.news.publish', $news) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-success-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-success-600">Publish</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No news found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $newsItems])
    </div>
@endsection
