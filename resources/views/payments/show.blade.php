@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $profile = $user->profile;
        $profilePhoto = $profile?->photo ? asset('storage/' . $profile->photo) : null;
        $userInitial = strtoupper(substr($user->name ?: 'U', 0, 1));

        $subscriptionStatus = $currentSubscription?->status?->value ?? \App\SubscriptionStatus::Unpaid->value;
        $isActiveSubscription =
            $subscriptionStatus === \App\SubscriptionStatus::Active->value &&
            ($currentSubscription?->ends_at === null || $currentSubscription->ends_at->isFuture());

        $badgeColorMap = [
            'active' => 'success',
            'pending' => 'warning',
            'expired' => 'warning',
            'unpaid' => 'error',
        ];
        $badgeColor = $badgeColorMap[$subscriptionStatus] ?? 'error';

        $subscriptionStatusKey = 'ui.subscriptions.status.' . $subscriptionStatus;
        $subscriptionStatusLabel = trans()->has($subscriptionStatusKey)
            ? __($subscriptionStatusKey)
            : ucfirst($subscriptionStatus);
    @endphp

    <div class="mx-auto w-full max-w-4xl p-4 md:p-8">
        {{-- User info card --}}
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span
                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                        @if ($profilePhoto)
                            <img src="{{ $profilePhoto }}" alt="Profile photo" class="h-full w-full object-cover" />
                        @else
                            {{ $userInitial }}
                        @endif
                    </span>

                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('profile.index') }}">
                        <x-ui.button variant="outline" size="sm">
                            {{ __('ui.dashboard.view_profile') }}
                        </x-ui.button>
                    </a>
                </div>
            </div>
        </div>

        {{-- Page title --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('ui.payments.title') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.payments.subtitle') }}
                    {{ $shouldChargeRegistrationFee ? __('ui.payments.one_time_plus_annual') : __('ui.payments.annual_renewal') }}
                </p>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <x-ui.alert variant="success" :title="__('ui.common.success')" :message="session('success')" class="mb-6" />
        @endif

        @if (session('warning'))
            <x-ui.alert variant="warning" :title="__('ui.common.notice')" :message="session('warning')" class="mb-6" />
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error" :title="__('ui.payments.payment_error')" :message="$errors->first()" class="mb-6" />
        @endif

        <div class="grid gap-6 lg:grid-cols-12">
            {{-- Main content --}}
            <div class="space-y-6 lg:col-span-8">
                @if (!$isActiveSubscription)
                    {{-- Membership package --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        @if ($shouldChargeRegistrationFee)
                            <div>
                                <span class="block font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                    {{ $registerPlan?->name ?? __('ui.payments.register_plan_default') }}
                                </span>
                                <h2 class="mt-1 font-bold text-gray-800 text-title-md dark:text-white/90">
                                    Rp {{ number_format((float) ($registerPlan?->price ?? 0), 0, ',', '.') }}
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $registerPlan?->description ?? __('ui.payments.register_plan_desc_default') }}
                                </p>
                            </div>

                            <div class="my-6 h-px w-full bg-gray-200 dark:bg-gray-800"></div>
                        @endif

                        <div>
                            <span class="block font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                {{ $annualPlan?->name ?? __('ui.payments.annual_plan_default') }}
                            </span>
                            <h2 class="mt-1 font-bold text-gray-800 text-title-md dark:text-white/90">
                                Rp {{ number_format((float) ($annualPlan?->price ?? 0), 0, ',', '.') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $annualPlan?->description ?? __('ui.payments.annual_plan_desc_default') }}
                            </p>

                            @if (!empty($annualPlan?->features))
                                <ul class="mt-6 space-y-3">
                                    @foreach ($annualPlan->features as $feature)
                                        <li class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="shrink-0 text-success-500" width="16" height="16"
                                                viewBox="0 0 16 16" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.4017 4.35986L6.12166 11.6399L2.59833 8.11657"
                                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- Order summary + Pay button --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                            {{ __('ui.payments.order_summary') }}
                        </h3>

                        <div class="mt-4 space-y-3">
                            @if ($shouldChargeRegistrationFee)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $registerPlan?->name ?? __('ui.payments.register_plan_default') }}
                                    </span>
                                    <span class="font-medium text-gray-800 dark:text-white/90">
                                        Rp {{ number_format((float) ($registerPlan?->price ?? 0), 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">
                                    {{ $annualPlan?->name ?? __('ui.payments.annual_plan_default') }}
                                </span>
                                <span class="font-medium text-gray-800 dark:text-white/90">
                                    Rp {{ number_format((float) ($annualPlan?->price ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="my-4 h-px w-full bg-gray-200 dark:bg-gray-800"></div>

                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-gray-800 dark:text-white/90">
                                {{ __('ui.payments.total') }}
                            </span>
                            <span class="font-bold text-gray-800 text-title-sm dark:text-white/90">
                                Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('payments.simulate') }}" class="mt-6">
                            @csrf
                            @if ($annualPlan)
                                <input type="hidden" name="plan_id" value="{{ $annualPlan->id }}">
                            @endif

                            <x-ui.button variant="primary" type="submit" className="w-full" :disabled="!$annualPlan">
                                {{ __('ui.payments.pay_now_demo') }}
                            </x-ui.button>
                        </form>
                    </div>
                @else
                    {{-- Active subscription state --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex items-start gap-4">
                            <span
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-success-50 text-success-500 dark:bg-success-500/10 dark:text-success-400">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                                    {{ __('ui.payments.subscription_active') }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('ui.payments.subscription_active_subtitle') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('dashboard') }}">
                                <x-ui.button variant="primary" size="md">
                                    {{ __('ui.payments.go_to_dashboard') }}
                                </x-ui.button>
                            </a>
                            <a href="{{ route('invoices.index') }}">
                                <x-ui.button variant="outline" size="md">
                                    {{ __('ui.payments.view_invoices') }}
                                </x-ui.button>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar: Current subscription status --}}
            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90">
                            {{ __('ui.payments.current_subscription') }}
                        </h3>
                        <div class="mt-2">
                            <x-ui.badge :color="$badgeColor" variant="light" size="sm">
                                {{ $subscriptionStatusLabel }}
                            </x-ui.badge>
                        </div>
                    </div>

                    <div class="my-4 h-px w-full bg-gray-200 dark:bg-gray-800"></div>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.plan') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $currentSubscription?->subscriptionPlan?->name ?? __('ui.common.none') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.starts') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ optional($currentSubscription?->starts_at)->format('d M Y') ?? '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.payments.ends') }}</dt>
                            <dd class="text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ optional($currentSubscription?->ends_at)->format('d M Y') ?? '-' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
