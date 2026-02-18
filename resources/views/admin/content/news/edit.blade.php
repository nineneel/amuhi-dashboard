@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.edit_news_article') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update: {{ $news->title }}</p>
            </div>
            <a href="{{ route('admin.news.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                Back to News
            </a>
        </div>

        @if ($errors->any())
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => 'Validation Error',
                'message' => $errors->first(),
            ])
        @endif

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        <form method="POST" action="{{ route('admin.news.update', $news) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    @component('admin.components.form-card', [
                        'title' => 'Article Content',
                        'description' => 'Edit the main content of the article.',
                    ])
                        <div class="space-y-5">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.title') }}</label>
                                <input type="text" name="title" value="{{ old('title', $news->title) }}" placeholder="{{ __('ui.admin.enter_article_title') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('title')
                                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.slug') }}</label>
                                <input type="text" name="slug" value="{{ old('slug', $news->slug) }}" placeholder="{{ __('ui.admin.article_url_slug') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('slug')
                                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.summary') }}</label>
                                <textarea name="summary" rows="3" placeholder="{{ __('ui.admin.brief_summary_of_the_article') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('summary', $news->summary) }}</textarea>
                                @error('summary')
                                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.content_blocks') }}</label>
                                @include('admin.components.content-editor', ['blocks' => old('content', $news->content ?? [])])
                                @error('content')
                                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @endcomponent
                </div>

                <div class="space-y-6 lg:col-span-1">
                    @component('admin.components.form-card', [
                        'title' => 'Article Settings',
                        'description' => 'Metadata and publishing options.',
                    ])
                        <div class="space-y-5">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.author_name') }}</label>
                                <input type="text" name="author_name" value="{{ old('author_name', $news->author_name) }}"
                                    placeholder="{{ __('ui.admin.author_s_name') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('author_name')
                                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.category') }}</label>
                                <input type="text" name="category" value="{{ old('category', $news->category) }}"
                                    placeholder="{{ __('ui.admin.e_g_berita_tips') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    {{ __('ui.admin.tags') }} <span class="text-gray-400">{{ __('ui.admin.comma_separated') }}</span>
                                </label>
                                <input type="text" name="tags_input"
                                    value="{{ old('tags_input', is_array($news->tags) ? implode(', ', $news->tags) : '') }}"
                                    placeholder="{{ __('ui.admin.tag1_tag2_tag3') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.badge') }} <span class="text-gray-400">{{ __('ui.admin.optional') }}</span></label>
                                <input type="text" name="badge" value="{{ old('badge', $news->badge) }}" placeholder="{{ __('ui.admin.e_g_hot_new') }}"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.read_time_minutes') }}</label>
                                <input type="number" name="read_time_minutes" value="{{ old('read_time_minutes', $news->read_time_minutes) }}" min="1"
                                    class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.status') }}</label>
                                <div class="relative">
                                    <select name="status"
                                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        @foreach ($statuses as $status)
                                            @php
                                                $currentStatus = is_object($news->status) ? $news->status->value : $news->status;
                                            @endphp
                                            <option value="{{ $status->value }}"
                                                {{ old('status', $currentStatus) === $status->value ? 'selected' : '' }}
                                                class="dark:bg-gray-900">
                                                {{ ucfirst($status->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    @endcomponent
                </div>
            </div>
        </form>
    </div>
@endsection
