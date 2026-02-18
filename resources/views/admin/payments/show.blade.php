@extends('admin.layouts.admin')

@section('content')
    @php
        $statusValue = is_object($payment->status) ? $payment->status->value : (string) $payment->status;
        $badgeType = match ($statusValue) {
            'success' => 'success',
            'pending' => 'warning',
            'failed' => 'error',
            default => 'neutral',
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.payment_details') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Payment #{{ $payment->id }}</p>
            </div>
            <a href="{{ route('admin.payments.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Back to Payments
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @component('admin.components.form-card', ['title' => 'Payment'])
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->gateway_transaction_id ?? '-' }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Created {{ $payment->created_at?->format('d M Y H:i') }}</p>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Amount'])
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format((float) $payment->amount, 2) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Paid at {{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</p>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Status'])
                @include('admin.components.badge', ['type' => $badgeType, 'text' => ucfirst($statusValue)])
            @endcomponent
        </div>

        @component('admin.components.form-card', ['title' => 'Related Invoice'])
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin.invoice_number') }}</p>
                    <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->invoice?->invoice_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin.member') }}</p>
                    <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->invoice?->user?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.admin.plan') }}</p>
                    <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->invoice?->subscription?->subscriptionPlan?->name ?? '-' }}</p>
                </div>
            </div>
        @endcomponent
    </div>
@endsection
