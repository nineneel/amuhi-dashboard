@extends('layouts.app')

@section('content')
    @php
        $statusClasses = [
            'paid' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'overdue' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
            'cancelled' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
        ];

        $totalAmount = $invoices->sum(fn ($invoice) => (float) $invoice->amount);
        $dueWithinThirtyDays = $invoices
            ->filter(function ($invoice) {
                if ($invoice->due_date === null) {
                    return false;
                }

                $status = $invoice->status?->value;

                if (! in_array($status, ['pending', 'overdue'], true)) {
                    return false;
                }

                return $invoice->due_date->isFuture() && $invoice->due_date->lte(now()->addDays(30));
            })
            ->count();
    @endphp

    <x-common.page-breadcrumb
        pageTitle="Invoices"
        :items="[
            ['label' => 'Profile', 'href' => route('profile.index')],
            ['label' => 'Invoices'],
        ]"
    />

    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-gray-800 dark:text-white/90">Overview</h2>
            </div>
            <div>
                <a href="{{ route('payments.show') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Manage Subscription
                </a>
            </div>
        </div>

        <div
            class="grid grid-cols-1 rounded-xl border border-gray-200 sm:grid-cols-2 lg:grid-cols-4 lg:divide-x lg:divide-y-0 dark:divide-gray-800 dark:border-gray-800">
            <div class="border-b p-5 sm:border-r lg:border-b-0">
                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">Overdue</p>
                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $summary['overdue'] }}</h3>
            </div>
            <div class="border-b p-5 lg:border-b-0">
                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">Due within next 30 days</p>
                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $dueWithinThirtyDays }}</h3>
            </div>
            <div class="border-b p-5 sm:border-r sm:border-b-0">
                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">Paid Invoices</p>
                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $summary['paid'] }}</h3>
            </div>
            <div class="p-5">
                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">Total Amount</p>
                <h3 class="text-3xl text-gray-800 dark:text-white/90">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Invoices</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your most recent invoices list</p>
            </div>
        </div>

        @if ($invoices->isEmpty())
            <div class="px-5 py-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">No invoices available for your account.</p>
            </div>
        @else
            <div class="custom-scrollbar overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border-b border-gray-200 dark:divide-gray-800 dark:border-gray-800">
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">Invoice Number</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">Creation Date</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">Due Date</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">Plan</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">Total</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">Status</th>
                            <th class="p-4 text-left text-xs font-medium text-gray-700 dark:text-gray-400">
                                <span class="sr-only">Action</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-x divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach ($invoices as $invoice)
                            @php
                                $invoiceStatus = $invoice->status?->value ?? 'pending';
                            @endphp
                            <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900">
                                <td class="p-4 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $invoice) }}"
                                        class="text-theme-xs font-medium text-gray-700 hover:underline dark:text-gray-400">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400">
                                    {{ $invoice->created_at->format('d M Y') }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400">
                                    {{ optional($invoice->due_date)->format('d M Y') ?? '-' }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400">
                                    {{ $invoice->subscription?->subscriptionPlan?->name ?? 'Membership' }}
                                </td>
                                <td class="p-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-400">
                                    Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$invoiceStatus] ?? $statusClasses['pending'] }}">
                                        {{ ucfirst($invoiceStatus) }}
                                    </span>
                                </td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('invoices.show', $invoice) }}"
                                            class="shadow-theme-xs inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                                            Details
                                        </a>
                                        <a href="{{ route('invoices.download', $invoice) }}"
                                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center rounded-lg px-3 py-2 text-xs font-medium text-white transition">
                                            Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
