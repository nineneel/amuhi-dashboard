@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">User Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Viewing profile for {{ $user->name }}.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Edit User
                </a>
                <a href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">Profile Info</h2>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Name</span>
                            <span class="w-2/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Email</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $user->email }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Role</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">
                                {{ ucwords(str_replace('_', ' ', is_object($user->role) ? $user->role->value : $user->role)) }}
                            </span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Joined</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $user->created_at?->format('d M Y') }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Verified</span>
                            <span class="w-2/3">
                                @if ($user->email_verified_at)
                                    @include('admin.components.badge', ['type' => 'success', 'text' => 'Verified'])
                                @else
                                    @include('admin.components.badge', ['type' => 'warning', 'text' => 'Unverified'])
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">Subscriptions</h2>
                    @if ($user->subscriptions->isEmpty())
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No subscriptions found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Plan</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Started</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Ends</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->subscriptions as $subscription)
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="py-3 text-theme-sm text-gray-700 dark:text-gray-300">
                                                {{ $subscription->subscriptionPlan?->name ?? '-' }}
                                            </td>
                                            <td class="py-3">
                                                @include('admin.components.badge', [
                                                    'type' => $subscription->status === 'active' ? 'success' : 'neutral',
                                                    'text' => ucfirst($subscription->status),
                                                ])
                                            </td>
                                            <td class="py-3 text-theme-sm text-gray-500 dark:text-gray-400">
                                                {{ $subscription->starts_at?->format('d M Y') ?? '-' }}
                                            </td>
                                            <td class="py-3 text-theme-sm text-gray-500 dark:text-gray-400">
                                                {{ $subscription->ends_at?->format('d M Y') ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">Invoices</h2>
                    @if ($user->invoices->isEmpty())
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No invoices found.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Invoice #</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user->invoices as $invoice)
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="py-3 text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                                <a href="{{ route('admin.invoices.show', $invoice) }}"
                                                    class="text-brand-500 hover:text-brand-600">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td class="py-3 text-theme-sm text-gray-700 dark:text-gray-300">
                                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3">
                                                @include('admin.components.badge', [
                                                    'type' => match ($invoice->status) {
                                                        'paid' => 'success',
                                                        'pending' => 'warning',
                                                        default => 'danger',
                                                    },
                                                    'text' => ucfirst($invoice->status),
                                                ])
                                            </td>
                                            <td class="py-3 text-theme-sm text-gray-500 dark:text-gray-400">
                                                {{ $invoice->created_at?->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
