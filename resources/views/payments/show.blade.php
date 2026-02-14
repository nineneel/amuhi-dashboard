@extends('layouts.fullscreen-layout')

@section('content')
    @php
        $profile = $user->profile;
        $profilePhoto = $profile?->photo ? asset('storage/' . $profile->photo) : null;
        $userInitial = strtoupper(substr($user->name ?: 'U', 0, 1));

        $subscriptionStatus = $currentSubscription?->status?->value ?? \App\SubscriptionStatus::Unpaid->value;
        $isActiveSubscription = $subscriptionStatus === \App\SubscriptionStatus::Active->value
            && ($currentSubscription?->ends_at === null || $currentSubscription->ends_at->isFuture());

        $statusClasses = [
            'active' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'expired' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'unpaid' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];
    @endphp

    <div class="mx-auto w-full max-w-4xl p-4 md:p-8">
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
                    <a href="{{ route('profile.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                        View Profile
                    </a>
                </div>
            </div>
        </div>

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Payment</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Complete your payment to activate your account and unlock access to Dashboard, Events, and Programs.
                </p>
            </div>
        </div>

        @if (session('success'))
            <x-ui.alert variant="success" title="Success" :message="session('success')" class="mb-6" />
        @endif

        @if (session('warning'))
            <x-ui.alert variant="warning" title="Notice" :message="session('warning')" class="mb-6" />
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error" title="Payment error" :message="$errors->first()" class="mb-6" />
        @endif

        <div class="grid gap-6 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Membership Package</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $shouldChargeRegistrationFee ? 'One-time registration fee plus annual membership.' : 'Annual membership renewal.' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                            <p class="text-xl font-semibold text-brand-600 dark:text-brand-400">
                                Rp {{ number_format((float) $totalAmount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 divide-y divide-gray-200 rounded-xl border border-gray-200 dark:divide-gray-800 dark:border-gray-800">
                        @if ($shouldChargeRegistrationFee)
                            <div class="flex items-start justify-between gap-4 p-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $registerPlan?->name ?? 'Register as Member' }}
                                    </p>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $registerPlan?->description ?? 'One-time registration fee' }}
                                    </p>
                                </div>
                                <p class="shrink-0 text-sm font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format((float) ($registerPlan?->price ?? 0), 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                        <div class="flex items-start justify-between gap-4 p-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $annualPlan?->name ?? 'Annual Membership' }}
                                </p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $annualPlan?->description ?? 'Annual access to member benefits' }}
                                </p>
                                @if (! empty($annualPlan?->features))
                                    <ul class="mt-3 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                        @foreach ($annualPlan->features as $feature)
                                            <li>• {{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format((float) ($annualPlan?->price ?? 0), 0, ',', '.') }}
                                </p>
                                @if (($annualPlan?->duration_days ?? 0) > 0)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        {{ (int) $annualPlan->duration_days }} days
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if (! $isActiveSubscription)
                        <form method="POST" action="{{ route('payments.simulate') }}" class="mt-6">
                            @csrf
                            @if ($annualPlan)
                                <input type="hidden" name="plan_id" value="{{ $annualPlan->id }}">
                            @endif

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
                                @disabled(! $annualPlan)>
                                Pay Now (Demo)
                            </button>
                        </form>
                    @endif
                </div>

                @if ($isActiveSubscription)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Your subscription is active.</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            You can access Events, Programs, and the Dashboard.
                        </p>
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-600 sm:w-auto">
                                Go to Dashboard
                            </a>
                            <a href="{{ route('invoices.index') }}"
                                class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03] sm:w-auto">
                                View Invoices
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6 lg:col-span-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="mb-3 text-base font-semibold text-gray-900 dark:text-white">Current Subscription</h3>
                    <span
                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$subscriptionStatus] ?? $statusClasses['unpaid'] }}">
                        {{ ucfirst($subscriptionStatus) }}
                    </span>

                    <dl class="mt-4 space-y-3">
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Plan</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $currentSubscription?->subscriptionPlan?->name ?? 'None' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Starts</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($currentSubscription?->starts_at)->format('d M Y') ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500 dark:text-gray-400">Ends</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($currentSubscription?->ends_at)->format('d M Y') ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
