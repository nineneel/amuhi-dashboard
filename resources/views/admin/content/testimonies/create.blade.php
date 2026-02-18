@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6" x-data="testimonyForm()">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.create_testimony') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.add_a_new_video_testimony_entry') }}</p>
            </div>
            <a href="{{ route('admin.testimonies.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Back to Testimonies
            </a>
        </div>

        @if ($errors->any())
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => 'Validation Error',
                'message' => $errors->first(),
            ])
        @endif

        <form method="POST" action="{{ route('admin.testimonies.store') }}" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @csrf

            <div class="space-y-6 lg:col-span-2">
                @component('admin.components.form-card', [
                    'title' => 'Testimony Content',
                    'description' => 'Main quote and speaker information.',
                ])
                    <div class="space-y-5">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.quote_block') }}</label>
                            <textarea name="text" rows="4" x-model="quoteText"
                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('text') }}</textarea>
                            @error('text')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.name') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" x-model="personName"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                @error('name')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.role') }}</label>
                                <input type="text" name="role" value="{{ old('role') }}" x-model="personRole"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                @error('role')
                                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>

            <div class="space-y-6">
                @component('admin.components.form-card', [
                    'title' => 'Media and Status',
                    'description' => 'Video source and visibility.',
                ])
                    <div class="space-y-5">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.admin.youtube_url') }}</label>
                            <input type="url" name="video_url" value="{{ old('video_url') }}" x-model="videoUrl"
                                placeholder="{{ __('ui.admin.https_youtube_com_watch_v') }}"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                            @error('video_url')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                                class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('ui.admin.active_testimony') }}</span>
                        </label>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/30">
                            <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('ui.admin.preview') }}</p>
                            <template x-if="embedUrl">
                                <iframe class="aspect-video w-full rounded-lg" :src="embedUrl" title="YouTube preview"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </template>
                            <template x-if="!embedUrl">
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.paste_a_valid_youtube_url_to_preview') }}</p>
                            </template>
                        </div>

                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                            Create Testimony
                        </button>
                    </div>
                @endcomponent
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function testimonyForm() {
            return {
                quoteText: @js(old('text', '')),
                personName: @js(old('name', '')),
                personRole: @js(old('role', '')),
                videoUrl: @js(old('video_url', '')),
                get embedUrl() {
                    const url = this.videoUrl || '';
                    const match = url.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/);

                    if (! match) {
                        return '';
                    }

                    return `https://www.youtube.com/embed/${match[1]}`;
                },
            };
        }
    </script>
@endpush
