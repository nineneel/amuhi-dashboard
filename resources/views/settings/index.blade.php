@extends('layouts.app')

@section('content')
    @php
        $settings = $user->settings;
        $privacySettings = $settings?->privacy_settings ?? [];

        $sectionTabs = [
            'account' => __('ui.settings.tabs.account'),
            'notifications' => __('ui.settings.tabs.notifications'),
            'privacy' => __('ui.settings.tabs.privacy'),
            'appearance' => __('ui.settings.tabs.appearance'),
            'security' => __('ui.settings.tabs.security'),
            'danger' => __('ui.settings.tabs.danger'),
        ];
    @endphp

    <x-common.page-breadcrumb :pageTitle="__('ui.settings.title')" />

    @if (session('success'))
        <x-ui.alert variant="success" :title="__('ui.common.saved')" :message="session('success')" class="mb-6" />
    @endif

    @if ($errors->any())
        <x-ui.alert variant="error" :title="__('ui.settings.unable_to_save')" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="grid gap-6 xl:grid-cols-12">
	        <div class="xl:col-span-3">
	            <div class="rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-white/[0.03]">
	                <nav class="space-y-1">
	                    @foreach ($sectionTabs as $sectionKey => $label)
	                        <a href="{{ route('settings.index', ['section' => $sectionKey]) }}"
	                            class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $section === $sectionKey ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' }}">
	                            {{ $label }}
	                        </a>
	                    @endforeach
	                </nav>

	                <div class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-800">
	                    <form method="POST" action="{{ route('logout') }}">
	                        @csrf
	                        <button type="submit"
	                            class="flex w-full items-center rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5">
	                            {{ __('ui.buttons.sign_out') }}
	                        </button>
	                    </form>
	                </div>
	            </div>
	        </div>

        <div class="xl:col-span-9">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                @if ($section === 'account')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('ui.settings.account_title') }}</h3>
                    <form method="POST" action="{{ route('settings.account') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.forms.name') }}</label>
                                <input type="text" value="{{ $user->name }}" disabled
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.forms.email') }}</label>
                                <input type="text" value="{{ $user->email }}" disabled
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                            </div>

                            <x-form.select :label="__('ui.forms.language')" name="language" id="language">
                                <option value="en" @selected(($settings?->language ?? config('app.locale')) === 'en')>English</option>
                                <option value="id" @selected(($settings?->language ?? config('app.locale')) === 'id')>Bahasa Indonesia</option>
                            </x-form.select>
                        </div>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.buttons.save_account_settings') }}
                        </button>
                    </form>
                @endif

                @if ($section === 'notifications')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('ui.settings.notifications_title') }}</h3>
                    <form method="POST" action="{{ route('settings.notifications') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <x-form.checkbox
                            name="notification_email"
                            :checked="$settings?->notification_email ?? true"
                            containerClass="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800"
                            labelClass="block"
                        >
                            <span class="block text-sm font-medium text-gray-800 dark:text-white/90">{{ __('ui.settings.notification_email_title') }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.settings.notification_email_desc') }}</span>
                        </x-form.checkbox>

                        <x-form.checkbox
                            name="notification_app"
                            :checked="$settings?->notification_app ?? true"
                            containerClass="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800"
                            labelClass="block"
                        >
                            <span class="block text-sm font-medium text-gray-800 dark:text-white/90">{{ __('ui.settings.notification_app_title') }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.settings.notification_app_desc') }}</span>
                        </x-form.checkbox>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.buttons.save_notification_settings') }}
                        </button>
                    </form>
                @endif

                @if ($section === 'privacy')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('ui.settings.privacy_title') }}</h3>
                    <form method="POST" action="{{ route('settings.privacy') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <x-form.checkbox
                            name="profile_visible"
                            :checked="(bool) ($privacySettings['profile_visible'] ?? true)"
                            containerClass="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800"
                            labelClass="text-sm text-gray-700 dark:text-gray-300"
                        >
                            {{ __('ui.settings.privacy_profile_visible') }}
                        </x-form.checkbox>

                        <x-form.checkbox
                            name="show_email"
                            :checked="(bool) ($privacySettings['show_email'] ?? false)"
                            containerClass="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800"
                            labelClass="text-sm text-gray-700 dark:text-gray-300"
                        >
                            {{ __('ui.settings.privacy_show_email') }}
                        </x-form.checkbox>

                        <x-form.checkbox
                            name="show_phone"
                            :checked="(bool) ($privacySettings['show_phone'] ?? false)"
                            containerClass="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800"
                            labelClass="text-sm text-gray-700 dark:text-gray-300"
                        >
                            {{ __('ui.settings.privacy_show_phone') }}
                        </x-form.checkbox>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.buttons.save_privacy_settings') }}
                        </button>
                    </form>
                @endif

                @if ($section === 'appearance')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('ui.settings.appearance_title') }}</h3>
                    <form method="POST" action="{{ route('settings.appearance') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <x-form.select :label="__('ui.forms.theme_preference')" name="theme" id="theme">
                            <option value="light" @selected(($settings?->theme ?? 'light') === 'light')>{{ __('ui.settings.theme.light') }}</option>
                            <option value="dark" @selected(($settings?->theme ?? 'light') === 'dark')>{{ __('ui.settings.theme.dark') }}</option>
                            <option value="system" @selected(($settings?->theme ?? 'light') === 'system')>{{ __('ui.settings.theme.system') }}</option>
                        </x-form.select>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.buttons.save_appearance') }}
                        </button>
                    </form>
                @endif

                @if ($section === 'security')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('ui.settings.security_title') }}</h3>

                    <div class="mb-6 rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ __('ui.settings.two_factor_title') }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.settings.two_factor_subtitle') }}</p>
                        <a href="{{ route('two-factor.index') }}"
                            class="mt-3 inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                            {{ __('ui.buttons.manage_two_factor') }}
                        </a>
                    </div>

                    <form method="POST" action="{{ route('settings.password') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <x-form.input :label="__('ui.forms.current_password')" type="password" name="current_password" id="current_password"
                            :placeholder="__('ui.forms.placeholder.enter_current_password')" required />
                        <x-form.input :label="__('ui.forms.new_password')" type="password" name="password" id="password"
                            :placeholder="__('ui.forms.placeholder.enter_new_password')" required />
                        <x-form.input :label="__('ui.forms.confirm_new_password')" type="password" name="password_confirmation"
                            id="password_confirmation" :placeholder="__('ui.forms.placeholder.confirm_new_password')" required />

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.buttons.change_password') }}
                        </button>
                    </form>
                @endif

                @if ($section === 'danger')
                    <h3 class="mb-4 text-lg font-semibold text-error-600 dark:text-error-400">{{ __('ui.settings.danger_title') }}</h3>
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('ui.settings.danger_description') }}
                    </p>

                    <form method="POST" action="{{ route('settings.destroy') }}" class="space-y-5">
                        @csrf
                        @method('DELETE')

                        <x-form.input :label="__('ui.forms.confirm_password')" type="password" name="password" id="delete_password"
                            :placeholder="__('ui.forms.placeholder.enter_password_to_confirm')" required />

                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg border border-error-300 bg-error-50 px-4 py-2.5 text-sm font-medium text-error-700 transition hover:bg-error-100 dark:border-error-500/40 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                            {{ __('ui.buttons.delete_my_account') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
