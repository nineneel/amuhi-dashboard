@extends('layouts.app')

@section('content')
    @php
        $settings = $user->settings;
        $privacySettings = $settings?->privacy_settings ?? [];

        $sectionTabs = [
            'account' => 'Account',
            'notifications' => 'Notifications',
            'privacy' => 'Privacy',
            'appearance' => 'Appearance',
            'security' => 'Security',
            'danger' => 'Danger Zone',
        ];
    @endphp

    <x-common.page-breadcrumb pageTitle="Settings" />

    @if (session('success'))
        <x-ui.alert variant="success" title="Saved" :message="session('success')" class="mb-6" />
    @endif

    @if ($errors->any())
        <x-ui.alert variant="error" title="Unable to save settings" :message="$errors->first()" class="mb-6" />
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
            </div>
        </div>

        <div class="xl:col-span-9">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                @if ($section === 'account')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Account Settings</h3>
                    <form method="POST" action="{{ route('settings.account') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Name</label>
                                <input type="text" value="{{ $user->name }}" disabled
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                            </div>

                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                                <input type="text" value="{{ $user->email }}" disabled
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" />
                            </div>

                            <div>
                                <label for="language" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Language</label>
                                <select id="language" name="language"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    <option value="en" @selected(($settings?->language ?? 'en') === 'en')>English</option>
                                    <option value="id" @selected(($settings?->language ?? 'en') === 'id')>Bahasa Indonesia</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Save Account Settings
                        </button>
                    </form>
                @endif

                @if ($section === 'notifications')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Notification Preferences</h3>
                    <form method="POST" action="{{ route('settings.notifications') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <input type="checkbox" name="notification_email" value="1"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                @checked($settings?->notification_email ?? true)>
                            <span>
                                <span class="block text-sm font-medium text-gray-800 dark:text-white/90">Email notifications</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Receive notifications via email.</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <input type="checkbox" name="notification_app" value="1"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                @checked($settings?->notification_app ?? true)>
                            <span>
                                <span class="block text-sm font-medium text-gray-800 dark:text-white/90">In-app notifications</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Show notifications in the dashboard UI.</span>
                            </span>
                        </label>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Save Notification Settings
                        </button>
                    </form>
                @endif

                @if ($section === 'privacy')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Privacy Settings</h3>
                    <form method="POST" action="{{ route('settings.privacy') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <input type="checkbox" name="profile_visible" value="1"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                @checked((bool) ($privacySettings['profile_visible'] ?? true))>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Allow other users to view my profile.</span>
                        </label>

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <input type="checkbox" name="show_email" value="1"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                @checked((bool) ($privacySettings['show_email'] ?? false))>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Show my email in profile details.</span>
                        </label>

                        <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <input type="checkbox" name="show_phone" value="1"
                                class="mt-1 h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                @checked((bool) ($privacySettings['show_phone'] ?? false))>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Show my phone number in profile details.</span>
                        </label>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Save Privacy Settings
                        </button>
                    </form>
                @endif

                @if ($section === 'appearance')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Appearance</h3>
                    <form method="POST" action="{{ route('settings.appearance') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="theme" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Theme preference</label>
                            <select id="theme" name="theme"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <option value="light" @selected(($settings?->theme ?? 'light') === 'light')>Light</option>
                                <option value="dark" @selected(($settings?->theme ?? 'light') === 'dark')>Dark</option>
                                <option value="system" @selected(($settings?->theme ?? 'light') === 'system')>System</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Save Appearance
                        </button>
                    </form>
                @endif

                @if ($section === 'security')
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Security</h3>

                    <div class="mb-6 rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Two-Factor Authentication</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enable or disable two-factor login protection.</p>
                        <a href="{{ route('two-factor.index') }}"
                            class="mt-3 inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                            Manage Two-Factor
                        </a>
                    </div>

                    <form method="POST" action="{{ route('settings.password') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <x-form.input label="Current Password" type="password" name="current_password" id="current_password"
                            placeholder="Enter current password" required />
                        <x-form.input label="New Password" type="password" name="password" id="password"
                            placeholder="Enter new password" required />
                        <x-form.input label="Confirm New Password" type="password" name="password_confirmation"
                            id="password_confirmation" placeholder="Confirm new password" required />

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Change Password
                        </button>
                    </form>
                @endif

                @if ($section === 'danger')
                    <h3 class="mb-4 text-lg font-semibold text-error-600 dark:text-error-400">Delete Account</h3>
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        This action is permanent. All your profile and account data will be removed.
                    </p>

                    <form method="POST" action="{{ route('settings.destroy') }}" class="space-y-5">
                        @csrf
                        @method('DELETE')

                        <x-form.input label="Confirm Password" type="password" name="password" id="delete_password"
                            placeholder="Enter password to confirm" required />

                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg border border-error-300 bg-error-50 px-4 py-2.5 text-sm font-medium text-error-700 transition hover:bg-error-100 dark:border-error-500/40 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20">
                            Delete My Account
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
