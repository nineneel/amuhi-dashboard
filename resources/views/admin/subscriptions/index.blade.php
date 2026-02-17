@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Subscriptions</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitor member subscription status.</p>
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'User', 'key' => 'user'],
                ['label' => 'Plan', 'key' => 'plan'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Starts', 'key' => 'starts_at'],
                ['label' => 'Ends', 'key' => 'ends_at'],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
        ])
            @forelse ($subscriptions as $subscription)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">{{ $subscription->user?->email }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $subscription->subscriptionPlan?->name }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ ucfirst($subscription->status->value ?? $subscription->status) }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $subscription->starts_at?->format('d M Y') ?? '-' }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $subscription->ends_at?->format('d M Y') ?? '-' }}</td>
                    <td class="px-5 py-4 sm:px-6">
                        <a href="{{ route('admin.subscriptions.show', $subscription) }}"
                            class="rounded-lg bg-brand-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-brand-600">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No subscriptions found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $subscriptions])
    </div>
@endsection
