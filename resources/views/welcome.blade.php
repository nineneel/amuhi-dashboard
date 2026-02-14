@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-12">
        <x-common.common-grid-shape />

        <div class="relative z-10 w-full max-w-5xl rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:p-10">
            <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="mb-4 inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        {{ __('ui.welcome.badge') }}
                    </p>
                    <h1 class="mb-4 text-3xl font-semibold text-gray-900 dark:text-white sm:text-4xl">
                        {{ __('ui.welcome.title') }}
                    </h1>
                    <p class="mb-8 text-sm text-gray-600 dark:text-gray-400 sm:text-base">
                        {{ __('ui.welcome.subtitle') }}
                    </p>

                    <div class="flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-brand-600">
                                {{ __('ui.welcome.open_dashboard') }}
                            </a>
                            <a href="{{ route('profile.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                                {{ __('ui.welcome.view_profile') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-brand-600">
                                {{ __('ui.buttons.sign_in') }}
                            </a>
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                                {{ __('ui.welcome.create_account') }}
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.welcome.card_dashboard_title') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.welcome.card_dashboard_desc') }}</p>
                    </x-ui.card>
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.welcome.card_events_title') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.welcome.card_events_desc') }}</p>
                    </x-ui.card>
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.welcome.card_invoices_title') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.welcome.card_invoices_desc') }}</p>
                    </x-ui.card>
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.welcome.card_settings_title') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.welcome.card_settings_desc') }}</p>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>
@endsection
