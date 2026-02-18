@php
    use App\InvoiceStatus;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.invoices') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.review_billing_records_and_payment_status') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.invoices.index'),
                'searchPlaceholder' => 'Search by invoice number or user',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => collect(InvoiceStatus::cases())->mapWithKeys(fn ($status) => [$status->value => ucfirst($status->value)])->toArray(),
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
                ['label' => 'Invoice'],
                ['label' => 'Member'],
                ['label' => 'Amount'],
                ['label' => 'Status'],
                ['label' => 'Actions'],
            ],
            'paginator' => $invoices,
        ])
            @forelse ($invoices as $invoice)
                @php
                    $statusValue = is_object($invoice->status) ? $invoice->status->value : (string) $invoice->status;
                    $badgeType = match ($statusValue) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'error',
                        default => 'neutral',
                    };
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $invoice->invoice_number }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Due {{ $invoice->due_date?->format('d M Y') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->user?->name ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format((float) $invoice->amount, 2) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $badgeType, 'text' => ucfirst($statusValue)])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'viewUrl' => route('admin.invoices.show', $invoice),
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_invoices_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
