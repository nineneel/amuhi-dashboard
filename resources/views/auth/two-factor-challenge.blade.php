@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 py-12">
        <x-common.common-grid-shape />

        <div class="relative z-10 w-full max-w-xl rounded-2xl border border-gray-200 bg-white p-8 shadow-theme-lg dark:border-gray-800 dark:bg-gray-900 sm:p-10">
            <h1 class="mb-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ __('ui.two_factor.challenge_title') }}</h1>
            <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                {{ __('ui.two_factor.challenge_subtitle', ['email' => $maskedEmail]) }}
            </p>

            @if (session('status'))
                <x-ui.alert variant="success" :title="__('ui.two_factor.code_sent')" :message="session('status')" class="mb-5" />
            @endif

            @if ($errors->any())
                <x-ui.alert variant="error" :title="__('ui.two_factor.invalid_code')" :message="$errors->first()" class="mb-5" />
            @endif

            <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-5">
                @csrf
                <x-form.input :label="__('ui.two_factor.verification_code')" type="text" name="code" id="code"
                    :placeholder="__('ui.two_factor.code_placeholder')" required autofocus />

                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    {{ __('ui.two_factor.verify_continue') }}
                </button>
            </form>

            <div class="mt-5 grid gap-3">
                <form method="POST" action="{{ route('two-factor.challenge.resend') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        {{ __('ui.two_factor.resend_code') }}
                    </button>
                </form>

                <a href="{{ route('login') }}" class="text-center text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                    {{ __('ui.two_factor.back_to_sign_in') }}
                </a>
            </div>
        </div>
    </div>
@endsection
