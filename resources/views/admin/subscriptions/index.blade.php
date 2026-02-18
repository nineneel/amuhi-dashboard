@php
    use App\SubscriptionStatus;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.subscriptions') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.track_user_subscriptions_and_plan_assignments') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.subscriptions.index'),
                'searchPlaceholder' => 'Search by member name or email',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => collect(SubscriptionStatus::cases())->mapWithKeys(fn ($status) => [$status->value => ucfirst($status->value)])->toArray(),
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
                ['label' => 'Member'],
                ['label' => 'Plan'],
                ['label' => 'Status'],
                ['label' => 'Period'],
                ['label' => 'Actions'],
            ],
            'paginator' => $subscriptions,
        ])
            @forelse ($subscriptions as $subscription)
                @php
                    $statusValue = is_object($subscription->status) ? $subscription->status->value : (string) $subscription->status;
                    $badgeType = match ($statusValue) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'expired' => 'neutral',
                        default => 'neutral',
                    };
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.avatar-cell', [
                            'name' => $subscription->user?->name ?? __('ui.admin.unknown_user'),
                            'subtitle' => $subscription->user?->email,
                        ])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->subscriptionPlan?->name ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $badgeType, 'text' => ucfirst($statusValue)])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subscription->starts_at?->format('d M Y') ?? '-' }} - {{ $subscription->ends_at?->format('d M Y') ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'viewUrl' => route('admin.subscriptions.show', $subscription),
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_subscriptions_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
