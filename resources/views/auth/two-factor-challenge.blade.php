@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-12">
        <x-common.common-grid-shape />

        <div class="relative z-10 w-full max-w-xl rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:p-10">
            <h1 class="mb-3 text-2xl font-semibold text-gray-900 dark:text-white">Two-Factor Authentication</h1>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                Enter the 6-digit code sent to {{ $maskedEmail }} or use a recovery code.
            </p>

            @if (session('status'))
                <x-ui.alert variant="success" title="Code sent" :message="session('status')" class="mb-5" />
            @endif

            @if ($errors->any())
                <x-ui.alert variant="error" title="Invalid code" :message="$errors->first()" class="mb-5" />
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-5">
                @csrf
                <x-form.input label="Verification Code" type="text" name="code" id="code"
                    placeholder="123456 or recovery code" required autofocus />

                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Verify and Continue
                </button>
            </form>

            <div class="mt-5 grid gap-3">
                <form method="POST" action="{{ route('two-factor.challenge.resend') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        Resend Verification Code
                    </button>
                </form>

                <a href="{{ route('login') }}" class="text-center text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                    Back to Sign In
                </a>
            </div>
        </div>
    </div>
@endsection
