@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-12">
        <x-common.common-grid-shape />

        <div class="relative z-10 w-full max-w-5xl rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:p-10">
            <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="mb-4 inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        AMUHI Dashboard
                    </p>
                    <h1 class="mb-4 text-3xl font-semibold text-gray-900 dark:text-white sm:text-4xl">
                        Membership Dashboard for Events, Programs, and Invoices
                    </h1>
                    <p class="mb-8 text-sm text-gray-600 dark:text-gray-400 sm:text-base">
                        Manage your AMUHI membership status, profile data, event registrations, and subscription invoices in one place.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-brand-600">
                                Open Dashboard
                            </a>
                            <a href="{{ route('profile.index') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                                View Profile
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white transition hover:bg-brand-600">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                                Create Account
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">Dashboard</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Welcome panel, upcoming events, and quick actions.</p>
                    </x-ui.card>
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">Events</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Browse event schedules and track your registrations.</p>
                    </x-ui.card>
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">Invoices</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Monitor billing history, payment status, and due dates.</p>
                    </x-ui.card>
                    <x-ui.card>
                        <h3 class="mb-1 text-base font-semibold text-gray-800 dark:text-white/90">Settings</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Control privacy, notification, and account preferences.</p>
                    </x-ui.card>
                </div>
            </div>
        </div>
    </div>
@endsection
