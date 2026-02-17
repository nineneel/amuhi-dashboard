@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Subscription Details</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $subscription->user?->email }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Plan</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $subscription->subscriptionPlan?->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ ucfirst($subscription->status->value ?? $subscription->status) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Starts At</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $subscription->starts_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Ends At</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $subscription->ends_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <a href="{{ route('admin.subscriptions.index') }}"
            class="inline-flex rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Back
            to Subscriptions</a>
    </div>
@endsection
