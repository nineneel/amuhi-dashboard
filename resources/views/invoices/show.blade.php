@extends('layouts.app')

@section('content')
	    @php
	        $invoiceStatus = $invoice->status?->value ?? 'pending';
	        $invoiceStatusKey = 'ui.invoices.status_labels.'.$invoiceStatus;
	        $invoiceStatusLabel = trans()->has($invoiceStatusKey) ? __($invoiceStatusKey) : ucfirst($invoiceStatus);
	        $statusClasses = [
	            'paid' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
	            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
	            'overdue' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'cancelled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        ];
        $paymentStatusClasses = [
            'success' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'failed' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'refunded' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        ];

        $totalPaidAmount = $invoice->payments->sum(fn ($payment) => (float) $payment->amount);
        $outstandingAmount = max((float) $invoice->amount - $totalPaidAmount, 0);
        $profile = $invoice->user->profile;
        $memberAddress = $profile?->address ?: '-';
    @endphp

	    <x-common.page-breadcrumb
	        :pageTitle="__('ui.invoices.invoice_details')"
	        :items="[
	            ['label' => __('ui.profile.title'), 'href' => route('profile.index')],
	            ['label' => __('ui.invoices.title'), 'href' => route('invoices.index')],
	            ['label' => __('ui.invoices.invoice_details')],
	        ]"
	    />

	    <div class="w-full rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
	        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
	            <h3 class="text-theme-xl font-medium text-gray-800 dark:text-white/90">{{ __('ui.invoices.invoice') }}</h3>
	            <div class="flex items-center gap-3">
	                <h4 class="text-base font-medium text-gray-700 dark:text-gray-400">ID : #{{ $invoice->invoice_number }}</h4>
	                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$invoiceStatus] ?? $statusClasses['pending'] }}">
	                    {{ $invoiceStatusLabel }}
	                </span>
	            </div>
	        </div>

        <div class="p-5 xl:p-8">
	            <div class="mb-9 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
	                <div>
	                    <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.invoices.from') }}</span>
	                    <h5 class="mb-2 text-base font-semibold text-gray-800 dark:text-white/90">AMUHI Dashboard</h5>
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        Membership & Subscription Services <br />
                        Indonesia
                    </p>
	                    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.invoices.issued_on') }}</span>
	                    <span class="block text-sm text-gray-500 dark:text-gray-400">{{ $invoice->created_at->format('d M Y') }}</span>
	                </div>

                <div class="h-px w-full bg-gray-200 sm:h-[158px] sm:w-px dark:bg-gray-800"></div>

	                <div class="sm:text-right">
	                    <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.invoices.to') }}</span>
	                    <h5 class="mb-2 text-base font-semibold text-gray-800 dark:text-white/90">{{ $invoice->user->name }}</h5>
                    <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                        {{ $invoice->user->email }} <br />
                        {{ $memberAddress }}
                    </p>
	                    <span class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('ui.invoices.due_on') }}</span>
	                    <span class="block text-sm text-gray-500 dark:text-gray-400">{{ optional($invoice->due_date)->format('d M Y') ?? '-' }}</span>
	                </div>
	            </div>

	            <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
	                <table class="min-w-full text-left text-gray-700 dark:text-gray-400">
	                    <thead class="bg-gray-50 dark:bg-gray-900">
	                        <tr class="border-b border-gray-100 dark:border-gray-800">
	                            <th class="px-5 py-3 text-xs font-medium whitespace-nowrap text-gray-500 dark:text-gray-400">{{ __('ui.invoices.description') }}</th>
	                            <th class="px-5 py-3 text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.reference') }}</th>
	                            <th class="px-5 py-3 text-center text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.issue_date') }}</th>
	                            <th class="px-5 py-3 text-center text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.due_date') }}</th>
	                            <th class="px-5 py-3 text-right text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.total') }}</th>
	                        </tr>
	                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        <tr>
                            <td class="px-5 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $invoice->subscription?->subscriptionPlan?->name ?? 'Membership Subscription' }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $invoice->invoice_number }}</td>
                            <td class="px-5 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ $invoice->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ optional($invoice->due_date)->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3 text-right text-sm text-gray-500 dark:text-gray-400">
                                Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

	            @if ($invoice->payments->isNotEmpty())
	                <div class="mt-5 overflow-x-auto rounded-xl border border-gray-100 dark:border-gray-800">
	                    <table class="min-w-full text-left text-gray-700 dark:text-gray-400">
	                        <thead class="bg-gray-50 dark:bg-gray-900">
	                            <tr class="border-b border-gray-100 dark:border-gray-800">
	                                <th class="px-5 py-3 text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.transaction_id') }}</th>
	                                <th class="px-5 py-3 text-center text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.paid_at') }}</th>
	                                <th class="px-5 py-3 text-center text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.status') }}</th>
	                                <th class="px-5 py-3 text-right text-sm font-medium whitespace-nowrap text-gray-700 dark:text-gray-400">{{ __('ui.invoices.amount') }}</th>
	                            </tr>
	                        </thead>
	                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
	                            @foreach ($invoice->payments as $payment)
	                                @php
	                                    $paymentStatus = $payment->status?->value ?? 'pending';
	                                    $paymentStatusKey = 'ui.invoices.payment_status_labels.'.$paymentStatus;
	                                    $paymentStatusLabel = trans()->has($paymentStatusKey) ? __($paymentStatusKey) : ucfirst($paymentStatus);
	                                @endphp
	                                <tr>
                                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $payment->gateway_transaction_id ?? '-' }}</td>
                                    <td class="px-5 py-3 text-center text-sm text-gray-500 dark:text-gray-400">{{ optional($payment->paid_at)->format('d M Y, H:i') ?? '-' }}</td>
	                                    <td class="px-5 py-3 text-center text-sm">
	                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $paymentStatusClasses[$paymentStatus] ?? $paymentStatusClasses['pending'] }}">
	                                            {{ $paymentStatusLabel }}
	                                        </span>
	                                    </td>
                                    <td class="px-5 py-3 text-right text-sm text-gray-500 dark:text-gray-400">
                                        Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

	            <div class="my-6 flex justify-end border-b border-gray-100 pb-6 text-right dark:border-gray-800">
	                <div class="w-[220px]">
	                    <p class="mb-4 text-left text-sm font-medium text-gray-800 dark:text-white/90">{{ __('ui.invoices.order_summary') }}</p>
	                    <ul class="space-y-2">
	                        <li class="flex justify-between gap-5">
	                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.invoices.sub_total') }}</span>
	                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</span>
	                        </li>
	                        <li class="flex items-center justify-between">
	                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.invoices.payments_received') }}</span>
	                            <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</span>
	                        </li>
	                        <li class="flex items-center justify-between">
	                            <span class="font-medium text-gray-700 dark:text-gray-400">{{ __('ui.invoices.outstanding') }}</span>
	                            <span class="text-lg font-semibold text-gray-800 dark:text-white/90">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</span>
	                        </li>
	                    </ul>
	                </div>
	            </div>

	            <div class="flex items-center justify-end gap-3">
	                <a href="{{ route('invoices.index') }}"
	                    class="shadow-theme-xs flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
	                    {{ __('ui.invoices.back_to_invoices') }}
	                </a>

	                <a href="{{ route('invoices.download', $invoice) }}"
	                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white">
	                    {{ __('ui.invoices.download_invoice') }}
	                </a>
	            </div>
        </div>
    </div>
@endsection
