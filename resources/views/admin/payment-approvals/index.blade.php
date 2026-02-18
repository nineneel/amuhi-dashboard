@php
    use App\Enums\PaymentApprovalStatus;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.payment_approvals') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.review_and_approve_manual_payment_proofs') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.payment-approvals.index'),
                'searchPlaceholder' => 'ui.admin.search_by_transaction_or_user',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'ui.admin.status',
                        'options' => collect(PaymentApprovalStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])->toArray(),
                    ],
                    [
                        'name' => 'per_page',
                        'label' => 'ui.admin.rows',
                        'include_all_option' => false,
                        'options' => [
                            '10' => 'ui.admin.rows_10',
                            '15' => 'ui.admin.rows_15',
                            '25' => 'ui.admin.rows_25',
                            '50' => 'ui.admin.rows_50',
                        ],
                        'value' => (string) request('per_page', '15'),
                    ],
                ],
                'class' => 'grid-cols-1 md:grid-cols-5',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'ui.admin.payment'],
                ['label' => 'ui.admin.member'],
                ['label' => 'ui.admin.amount'],
                ['label' => 'ui.admin.status'],
                ['label' => 'ui.admin.actions'],
            ],
            'paginator' => $payments,
        ])
            @forelse ($payments as $payment)
                @php
                    $approvalStatus = $payment->currentApprovalStatus();
                    $isPendingReview = $approvalStatus === PaymentApprovalStatus::PendingReview;
                    $badgeType = $isPendingReview ? 'danger' : ($approvalStatus?->badgeColor() ?? 'neutral');
                    $statusText = $isPendingReview ? __('ui.admin.need_approval') : ($approvalStatus?->label() ?? __('ui.admin.unknown'));
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $payment->gateway_transaction_id ?? __('ui.admin.payment_id', ['id' => $payment->id]) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->created_at?->format('d M Y H:i') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-800 dark:text-white/90">{{ $payment->invoice?->user?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->invoice?->user?->email ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <div class="inline-flex items-center gap-2">
                            @if ($isPendingReview)
                                <span class="relative inline-flex h-2.5 w-2.5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-error-500 opacity-75"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-error-500"></span>
                                </span>
                            @endif
                            @include('admin.components.badge', ['type' => $badgeType, 'text' => $statusText])
                        </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'viewUrl' => route('admin.payment-approvals.show', $payment),
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_payment_approvals_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
