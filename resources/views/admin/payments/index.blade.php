@php
    use App\PaymentStatus;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.payments') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.monitor_incoming_payment_transactions') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.payments.index'),
                'searchPlaceholder' => 'Search by transaction or user',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => collect(PaymentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => ucfirst($status->value)])->toArray(),
                    ],
                    [
                        'name' => 'per_page',
                        'label' => 'Rows',
                        'include_all_option' => false,
                        'options' => [
                            '10' => '10 rows',
                            '15' => '15 rows',
                            '25' => '25 rows',
                            '50' => '50 rows',
                        ],
                        'value' => (string) request('per_page', '15'),
                    ],
                ],
                'class' => 'grid-cols-1 md:grid-cols-5',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Payment'],
                ['label' => 'Member'],
                ['label' => 'Amount'],
                ['label' => 'Status'],
                ['label' => 'Actions'],
            ],
            'paginator' => $payments,
        ])
            @forelse ($payments as $payment)
                @php
                    $statusValue = is_object($payment->status) ? $payment->status->value : (string) $payment->status;
                    $badgeType = match ($statusValue) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'error',
                        default => 'neutral',
                    };
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->gateway_transaction_id ?? __('ui.admin.payment_id', ['id' => $payment->id]) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->created_at?->format('d M Y H:i') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $payment->invoice?->user?->name ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format((float) $payment->amount, 2) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $badgeType, 'text' => ucfirst($statusValue)])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'viewUrl' => route('admin.payments.show', $payment),
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_payments_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
