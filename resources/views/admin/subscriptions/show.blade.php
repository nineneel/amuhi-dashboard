@extends('admin.layouts.admin')

@section('content')
    @php
        $statusValue = is_object($subscription->status) ? $subscription->status->value : (string) $subscription->status;
        $badgeType = match ($statusValue) {
            'active' => 'success',
            'pending' => 'warning',
            'expired' => 'neutral',
            default => 'neutral',
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.subscription_details') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.view_subscription_and_billing_activity') }}</p>
            </div>
            <a href="{{ route('admin.subscriptions.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Back to Subscriptions
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @component('admin.components.form-card', ['title' => 'Member'])
                @include('admin.components.avatar-cell', [
                    'name' => $subscription->user?->name ?? __('ui.admin.unknown_user'),
                    'subtitle' => $subscription->user?->email,
                    'size' => 'md',
                ])
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Plan'])
                <p class="text-sm text-gray-800 dark:text-white/90">{{ $subscription->subscriptionPlan?->name ?? '-' }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Subscription ID: {{ $subscription->gateway_subscription_id ?? '-' }}</p>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Status'])
                @include('admin.components.badge', ['type' => $badgeType, 'text' => ucfirst($statusValue)])
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $subscription->starts_at?->format('d M Y') ?? '-' }} - {{ $subscription->ends_at?->format('d M Y') ?? '-' }}</p>
            @endcomponent
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Invoice'],
                ['label' => 'Amount'],
                ['label' => 'Status'],
                ['label' => 'Due Date'],
            ],
        ])
            @forelse ($subscription->invoices as $invoice)
                @php
                    $invoiceStatus = is_object($invoice->status) ? $invoice->status->value : (string) $invoice->status;
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $invoice->invoice_number }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format((float) $invoice->amount, 2) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($invoiceStatus) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</p>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_invoices_for_this_subscription') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
