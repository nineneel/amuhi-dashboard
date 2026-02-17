@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Payment Details</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $payment->gateway_transaction_id ?? 'No transaction ID' }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Invoice</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->invoice?->invoice_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">User</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->invoice?->user?->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Amount</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format((float) $payment->amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ ucfirst($payment->status->value ?? $payment->status) }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
