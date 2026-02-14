@extends('layouts.app')

@section('content')
    @php
        $profile = $user->profile;
        $settings = $user->settings;
        $currentSubscription = $user->subscriptions->sortByDesc('created_at')->first();
        $subscriptionStatus = $currentSubscription?->status?->value ?? \App\SubscriptionStatus::Unpaid->value;

        $statusClasses = [
            'active' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'expired' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'unpaid' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];
    @endphp

    <x-common.page-breadcrumb pageTitle="Profile" />

    @if (session('success'))
        <x-ui.alert variant="success" title="Success" :message="session('success')" class="mb-6" />
    @endif

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="xl:col-span-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col items-center text-center">
                    @if ($profile?->photo)
                        <img src="{{ asset('storage/' . $profile->photo) }}" alt="Profile photo"
                            class="h-24 w-24 rounded-full object-cover" />
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-brand-50 text-2xl font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

                    <span class="mt-3 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$subscriptionStatus] ?? $statusClasses['unpaid'] }}">
                        Subscription: {{ ucfirst($subscriptionStatus) }}
                    </span>
                </div>

                <div class="mt-6 grid gap-3">
                    <a href="{{ route('profile.edit') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                        Edit Profile
                    </a>
                    <a href="{{ route('two-factor.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        Manage Two-Factor
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6 xl:col-span-8">
            <x-common.component-card title="Personal Information" desc="Your account and contact information.">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Member Type</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ strtoupper($profile?->member_type?->value ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Company / PT Name</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $profile?->company_name ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $profile?->phone ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Language</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ strtoupper($settings?->language ?? 'en') }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Address</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $profile?->address ?: '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bio</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $profile?->bio ?: '-' }}</p>
                    </div>
                </div>
            </x-common.component-card>

            <x-common.component-card title="Current Subscription" desc="Track your membership plan and period.">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Plan</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $currentSubscription?->subscriptionPlan?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ ucfirst($subscriptionStatus) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Starts At</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ optional($currentSubscription?->starts_at)->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ends At</p>
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ optional($currentSubscription?->ends_at)->format('d M Y') ?? '-' }}</p>
                    </div>
                </div>
                <div class="mt-5">
                    <a href="{{ route('invoices.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        View Invoices
                    </a>
                </div>
            </x-common.component-card>
        </div>
    </div>
@endsection
