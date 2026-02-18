@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Events</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage portal events and registrations.</p>
            </div>
            <a href="{{ route('admin.events.create') }}"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                Add Event
            </a>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.events.index'),
                'searchPlaceholder' => 'Search by title or location',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => collect($statuses)->mapWithKeys(fn ($status) => [$status->value => ucfirst($status->value)])->toArray(),
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
                ['label' => 'Event'],
                ['label' => 'Status'],
                ['label' => 'Starts'],
                ['label' => 'Actions'],
            ],
            'paginator' => $events,
        ])
            @forelse ($events as $event)
                @php
                    $statusValue = is_object($event->status) ? $event->status->value : (string) $event->status;
                    $statusBadge = match ($statusValue) {
                        'upcoming' => 'info',
                        'ongoing' => 'success',
                        'past' => 'neutral',
                        'cancelled' => 'error',
                        default => 'neutral',
                    };
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $event->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->location ?? 'No location' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $statusBadge, 'text' => ucfirst($statusValue)])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event->starts_at?->format('d M Y, H:i') ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'viewUrl' => route('admin.events.show', $event),
                            'editUrl' => route('admin.events.edit', $event),
                            'deleteUrl' => route('admin.events.destroy', $event),
                            'deleteConfirm' => 'Delete this event?',
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No events found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
