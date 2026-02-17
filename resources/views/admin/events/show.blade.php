@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ $event->title }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($event->status->value ?? $event->status) }}</p>
            </div>

            <a href="{{ route('admin.events.edit', $event) }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Edit Event</a>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Location</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $event->location ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Starts At</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $event->starts_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Ends At</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $event->ends_at?->format('d M Y H:i') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $event->description ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Registrant', 'key' => 'user'],
                ['label' => 'Email', 'key' => 'email'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Registered At', 'key' => 'registered_at'],
            ],
        ])
            @forelse ($event->eventRegistrations as $registration)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">{{ $registration->user?->name }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $registration->user?->email }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ ucfirst($registration->status) }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $registration->registered_at?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No registrations yet.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
