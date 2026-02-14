@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        :pageTitle="__('ui.two_factor.title')"
        :items="[
            ['label' => __('ui.profile.title'), 'href' => route('profile.index')],
            ['label' => __('ui.two_factor.title')],
        ]"
    />

    @if (session('status'))
        <x-ui.alert variant="success" :title="__('ui.common.success')" :message="session('status')" class="mb-6" />
    @endif

    @if ($errors->any())
        <x-ui.alert variant="error" :title="__('ui.settings.unable_to_save')" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <x-common.component-card :title="__('ui.two_factor.email_verification_code')" :desc="__('ui.two_factor.email_verification_desc')">
            @if ($twoFactorEnabled)
                <x-ui.alert variant="success" :title="__('ui.two_factor.two_factor_active')"
                    :message="__('ui.two_factor.two_factor_active_message')" />

                <form method="POST" action="{{ route('two-factor.disable') }}" class="pt-3">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg border border-error-300 px-4 py-2.5 text-sm font-medium text-error-600 transition hover:bg-error-50 dark:border-error-500/40 dark:text-error-400 dark:hover:bg-error-500/10">
                        {{ __('ui.two_factor.disable') }}
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.two_factor.send_code_to_email', ['email' => $maskedEmail]) }}
                </p>

                @if (! $enableCodeSent)
                    <form method="POST" action="{{ route('two-factor.enable.send') }}" class="pt-1">
                        @csrf
                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.two_factor.send_code') }}
                        </button>
                    </form>
                @else
                    <x-ui.alert variant="info" :title="__('ui.two_factor.code_sent')"
                        :message="__('ui.two_factor.code_sent_message', ['email' => $maskedEmail])" />

                    <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-4 pt-1">
                        @csrf
                        <x-form.input :label="__('ui.two_factor.verification_code')" type="text" name="code" id="code"
                            :placeholder="__('ui.two_factor.enter_6_digit')" required />

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            {{ __('ui.two_factor.enable') }}
                        </button>
                    </form>
                @endif
            @endif
        </x-common.component-card>

        <x-common.component-card :title="__('ui.two_factor.recovery_codes')" :desc="__('ui.two_factor.recovery_codes_desc')">
            @if (empty($recoveryCodes))
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.two_factor.recovery_codes_after_enabled') }}
                </p>
            @else
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($recoveryCodes as $recoveryCode)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                            {{ $recoveryCode }}
                        </div>
                    @endforeach
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.two_factor.recovery_codes_once') }}
                </p>
            @endif
        </x-common.component-card>
    </div>
@endsection
