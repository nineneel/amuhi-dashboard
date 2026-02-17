@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Events</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create and manage portal events.</p>
            </div>

            <a href="{{ route('admin.events.create') }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Create
                Event</a>
        </div>

        @if (session('status'))
            @include('admin.components.alert', ['type' => 'success', 'title' => 'Success', 'message' => session('status')])
        @endif

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Title', 'key' => 'title'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Starts', 'key' => 'starts_at'],
                ['label' => 'Registrations', 'key' => 'event_registrations_count'],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
        ])
            @forelse ($events as $event)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $event->title }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ ucfirst($event->status->value ?? $event->status) }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $event->starts_at?->format('d M Y H:i') ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $event->event_registrations_count }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.events.show', $event) }}"
                                class="rounded-lg bg-brand-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-brand-600">View</a>
                            <a href="{{ route('admin.events.edit', $event) }}"
                                class="rounded-lg bg-gray-700 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-gray-600">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No events found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $events])
    </div>
@endsection
