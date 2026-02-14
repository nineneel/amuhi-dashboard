@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="Two-Factor Authentication"
        :items="[
            ['label' => 'Profile', 'href' => route('profile.index')],
            ['label' => 'Two-Factor Authentication'],
        ]"
    />

    @if (session('status'))
        <x-ui.alert variant="success" title="Success" :message="session('status')" class="mb-6" />
    @endif

    @if ($errors->any())
        <x-ui.alert variant="error" title="Unable to update" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <x-common.component-card title="Email Verification Code" desc="Two-factor authentication is verified with a one-time code sent to your email.">
            @if ($twoFactorEnabled)
                <x-ui.alert variant="success" title="Two-factor is active"
                    message="Your account is protected with email verification and optional recovery codes." />

                <form method="POST" action="{{ route('two-factor.disable') }}" class="pt-3">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg border border-error-300 px-4 py-2.5 text-sm font-medium text-error-600 transition hover:bg-error-50 dark:border-error-500/40 dark:text-error-400 dark:hover:bg-error-500/10">
                        Disable Two-Factor Authentication
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    We will send a 6-digit verification code to <span class="font-medium text-gray-700 dark:text-gray-300">{{ $maskedEmail }}</span>.
                </p>

                @if (! $enableCodeSent)
                    <form method="POST" action="{{ route('two-factor.enable.send') }}" class="pt-1">
                        @csrf
                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Send Verification Code
                        </button>
                    </form>
                @else
                    <x-ui.alert variant="info" title="Code sent"
                        :message="'A verification code was sent to '.$maskedEmail.'. Enter it below to enable two-factor authentication.'" />

                    <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-4 pt-1">
                        @csrf
                        <x-form.input label="Verification Code" type="text" name="code" id="code"
                            placeholder="Enter 6-digit code" required />

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-white transition">
                            Enable Two-Factor Authentication
                        </button>
                    </form>
                @endif
            @endif
        </x-common.component-card>

        <x-common.component-card title="Recovery Codes" desc="Store these codes securely as backup login methods.">
            @if (empty($recoveryCodes))
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Recovery codes will appear after two-factor authentication is enabled.
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
                    Each recovery code can be used once if you cannot access your email code.
                </p>
            @endif
        </x-common.component-card>
    </div>
@endsection
