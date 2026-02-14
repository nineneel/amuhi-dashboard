@extends('layouts.app')

@section('content')
    @php
        $subscriptionStatus = $currentSubscription?->status?->value ?? \App\SubscriptionStatus::Unpaid->value;

        $statusClasses = [
            'active' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'expired' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'unpaid' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];
    @endphp

    <x-common.page-breadcrumb
        pageTitle="Payment"
        :items="[
            ['label' => 'Profile', 'href' => route('profile.index')],
            ['label' => 'Payment'],
        ]"
    />

    @if (session('success'))
        <x-ui.alert variant="success" title="Success" :message="session('success')" class="mb-6" />
    @endif

    @if (session('warning'))
        <x-ui.alert variant="warning" title="Notice" :message="session('warning')" class="mb-6" />
    @endif

    @if ($errors->any())
        <x-ui.alert variant="error" title="Payment error" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Subscription Plan</h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                    Select a plan, then simulate a successful payment to activate your account in demo mode.
                </p>

                <form method="POST" action="{{ route('payments.simulate') }}" class="space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($plans as $plan)
                            <label class="cursor-pointer rounded-xl border border-gray-200 p-4 transition hover:border-brand-300 dark:border-gray-800 dark:hover:border-brand-800">
                                <div class="flex items-start gap-3">
                                    <input type="radio" name="plan_id" value="{{ $plan->id }}"
                                        class="mt-1 h-4 w-4 border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700"
                                        @checked((int) old('plan_id', $selectedPlanId) === $plan->id)>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $plan->name }}</p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $plan->description }}</p>
                                        <p class="mt-2 text-sm font-medium text-brand-600 dark:text-brand-400">
                                            Rp {{ number_format((float) $plan->price, 0, ',', '.') }} / {{ $plan->duration_days }} days
                                        </p>
                                        @if (! empty($plan->features))
                                            <ul class="mt-2 space-y-1 text-xs text-gray-500 dark:text-gray-400">
                                                @foreach ($plan->features as $feature)
                                                    <li>• {{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                        Simulate Successful Payment
                    </button>
                </form>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">Manual Status Update (Demo)</h3>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                    Use this only for testing unpaid, pending, active, and expired states.
                </p>

                <form method="POST" action="{{ route('payments.status') }}" class="grid gap-4 sm:grid-cols-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="plan_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Plan</label>
                        <select id="plan_id" name="plan_id"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}" @selected((int) old('plan_id', $selectedPlanId) === $plan->id)>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                        <select id="status" name="status"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="unpaid">Unpaid</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6 xl:col-span-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">Current Subscription</h3>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$subscriptionStatus] ?? $statusClasses['unpaid'] }}">
                    {{ ucfirst($subscriptionStatus) }}
                </span>

                <dl class="mt-4 space-y-3">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Plan</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $currentSubscription?->subscriptionPlan?->name ?? 'None' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Starts</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-white/90">{{ optional($currentSubscription?->starts_at)->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Ends</dt>
                        <dd class="text-sm font-medium text-gray-800 dark:text-white/90">{{ optional($currentSubscription?->ends_at)->format('d M Y') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
