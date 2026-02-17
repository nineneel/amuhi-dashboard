@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Invoice {{ $invoice->invoice_number }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $invoice->user?->email }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <dl class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Amount</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ number_format((float) $invoice->amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ ucfirst($invoice->status->value ?? $invoice->status) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Due Date</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $invoice->due_date?->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Payments</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ $invoice->payments->count() }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
