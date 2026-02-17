@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Site Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage general, contact, and SEO settings.</p>
        </div>

        @if (session('status'))
            @include('admin.components.alert', ['type' => 'success', 'title' => 'Success', 'message' => session('status')])
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST"
            class="space-y-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">General</h2>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Site Name</label>
                    <input name="site_name" value="{{ old('site_name', $settings['site_name']) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                    @error('site_name')
                        <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tagline</label>
                    <input name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Contact</h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Email</label>
                        <input name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Phone</label>
                        <input name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                    <textarea name="contact_address" rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">{{ old('contact_address', $settings['contact_address']) }}</textarea>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">SEO</h2>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Title</label>
                    <input name="seo[meta_title]" value="{{ old('seo.meta_title', $settings['seo']['meta_title']) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Description</label>
                    <textarea name="seo[meta_description]" rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90">{{ old('seo.meta_description', $settings['seo']['meta_description']) }}</textarea>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Keyword</label>
                    <input name="seo[meta_keywords][]" value="{{ old('seo.meta_keywords.0', data_get($settings, 'seo.meta_keywords.0')) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection
