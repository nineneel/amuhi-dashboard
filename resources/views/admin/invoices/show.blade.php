@extends('admin.layouts.admin')

@section('content')
    @php
        $statusValue = is_object($invoice->status) ? $invoice->status->value : (string) $invoice->status;
        $badgeType = match ($statusValue) {
            'paid' => 'success',
            'pending' => 'warning',
            'overdue' => 'error',
            default => 'neutral',
        };
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Invoice Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Invoice {{ $invoice->invoice_number }}</p>
            </div>
            <a href="{{ route('admin.invoices.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">
                Back to Invoices
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @component('admin.components.form-card', ['title' => 'Invoice'])
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $invoice->invoice_number }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Due {{ $invoice->due_date?->format('d M Y') }}</p>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Member'])
                <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $invoice->user?->name ?? 'Unknown user' }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->user?->email ?? '-' }}</p>
            @endcomponent

            @component('admin.components.form-card', ['title' => 'Status'])
                @include('admin.components.badge', ['type' => $badgeType, 'text' => ucfirst($statusValue)])
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Amount: {{ number_format((float) $invoice->amount, 2) }}</p>
            @endcomponent
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Payment ID'],
                ['label' => 'Transaction'],
                ['label' => 'Amount'],
                ['label' => 'Status'],
                ['label' => 'Paid At'],
            ],
        ])
            @forelse ($invoice->payments as $payment)
                @php
                    $paymentStatus = is_object($payment->status) ? $payment->status->value : (string) $payment->status;
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-800 dark:text-white/90">#{{ $payment->id }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->gateway_transaction_id ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format((float) $payment->amount, 2) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($paymentStatus) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->paid_at?->format('d M Y H:i') ?? '-' }}</p>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No payments recorded for this invoice.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
