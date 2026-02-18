@extends('admin.layouts.admin')

@section('content')
    @php
        $groupedSettings = $settings;

        $defaults = [
            'general' => [
                'site_name' => ['label' => 'Site Name', 'value' => config('app.name')],
                'site_tagline' => ['label' => 'Site Tagline', 'value' => ''],
                'admin_email' => ['label' => 'Admin Email', 'value' => ''],
            ],
            'seo' => [
                'meta_title' => ['label' => 'Default Meta Title', 'value' => ''],
                'meta_description' => ['label' => 'Default Meta Description', 'value' => ''],
                'meta_keywords' => ['label' => 'Meta Keywords', 'value' => ''],
            ],
            'contact' => [
                'contact_email' => ['label' => 'Contact Email', 'value' => ''],
                'contact_phone' => ['label' => 'Contact Phone', 'value' => ''],
                'contact_address' => ['label' => 'Contact Address', 'value' => ''],
            ],
        ];

        foreach ($groupedSettings as $group => $items) {
            foreach ($items as $item) {
                $currentValue = is_array($item->value) ? json_encode($item->value) : (string) $item->value;

                $defaults[$group][$item->key] = [
                    'label' => str_replace('_', ' ', ucfirst($item->key)),
                    'value' => $currentValue,
                ];
            }
        }
    @endphp

    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Site Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update general, SEO, and contact configuration.</p>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        @if ($errors->any())
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => 'Validation Error',
                'message' => $errors->first(),
            ])
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            @foreach ($defaults as $group => $entries)
                @component('admin.components.form-card', [
                    'title' => ucfirst($group),
                    'description' => 'Configure '.ucfirst($group).' settings.',
                ])
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        @foreach ($entries as $key => $entry)
                            <div class="md:col-span-1 {{ str_contains($key, 'description') || str_contains($key, 'address') ? 'md:col-span-2' : '' }}">
                                <label for="settings_{{ $key }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $entry['label'] }}
                                </label>

                                @if (str_contains($key, 'description') || str_contains($key, 'address'))
                                    <textarea id="settings_{{ $key }}" name="settings[{{ $key }}]" rows="3"
                                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800">{{ old('settings.'.$key, $entry['value']) }}</textarea>
                                @else
                                    <input id="settings_{{ $key }}" type="text" name="settings[{{ $key }}]"
                                        value="{{ old('settings.'.$key, $entry['value']) }}"
                                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endcomponent
            @endforeach

            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                Save Settings
            </button>
        </form>
    </div>
@endsection
