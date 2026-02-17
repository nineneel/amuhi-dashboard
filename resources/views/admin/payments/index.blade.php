@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Payments</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track payment transactions.</p>
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Transaction', 'key' => 'gateway_transaction_id'],
                ['label' => 'Invoice', 'key' => 'invoice'],
                ['label' => 'Amount', 'key' => 'amount'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
        ])
            @forelse ($payments as $payment)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">{{ $payment->gateway_transaction_id ?? '-' }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $payment->invoice?->invoice_number }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ number_format((float) $payment->amount, 2) }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ ucfirst($payment->status->value ?? $payment->status) }}</td>
                    <td class="px-5 py-4 sm:px-6">
                        <a href="{{ route('admin.payments.show', $payment) }}"
                            class="rounded-lg bg-brand-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-brand-600">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No payments found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $payments])
    </div>
@endsection
