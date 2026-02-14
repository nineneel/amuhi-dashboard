@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-12">
        <x-common.common-grid-shape />

        <div class="relative z-10 w-full max-w-xl rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:p-10">
            <h1 class="mb-3 text-2xl font-semibold text-gray-900 dark:text-white">Verify Your Email</h1>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                We sent a verification link to your email. Verify your account to continue.
            </p>

            @if (session('status'))
                <x-ui.alert variant="success" title="Email sent" :message="session('status')" class="mb-5" />
            @endif

            @if (session('mail_error'))
                <x-ui.alert variant="warning" title="Delivery issue" :message="session('mail_error')" class="mb-5" />
            @endif

            @if ($errors->any())
                <x-ui.alert variant="error" title="Verification issue" :message="$errors->first()" class="mb-5" />
            @endif

            <div class="space-y-3">
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                        Resend Verification Link
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
