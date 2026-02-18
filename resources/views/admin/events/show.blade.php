@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Event Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $event->title }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.edit', $event) }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Edit Event
                </a>
                <a href="{{ route('admin.events.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Back to Events
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    @if ($event->image)
                        <div class="mb-4 overflow-hidden rounded-xl">
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}"
                                class="w-full object-cover" />
                        </div>
                    @endif
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">Event Info</h2>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Status</span>
                            <span class="w-2/3">
                                @php
                                    $statusValue = is_object($event->status) ? $event->status->value : $event->status;
                                    $statusBadge = match ($statusValue) {
                                        'upcoming' => 'info',
                                        'ongoing' => 'success',
                                        'ended' => 'neutral',
                                        default => 'neutral',
                                    };
                                @endphp
                                @include('admin.components.badge', ['type' => $statusBadge, 'text' => ucfirst($statusValue)])
                            </span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Location</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $event->location ?? '-' }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Starts</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $event->starts_at?->format('d M Y, H:i') ?? '-' }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Ends</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $event->ends_at?->format('d M Y, H:i') ?? '-' }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">Registrations</span>
                            <span class="w-2/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $event->eventRegistrations->count() }}</span>
                        </li>
                    </ul>
                    @if ($event->description)
                        <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
                            <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $event->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">Registrations</h2>
                    @if ($event->eventRegistrations->isEmpty())
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No registrations yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">#</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Name</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Email</th>
                                        <th class="pb-3 text-left text-theme-xs font-medium text-gray-500 dark:text-gray-400">Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($event->eventRegistrations as $index => $registration)
                                        <tr class="border-b border-gray-100 dark:border-gray-800">
                                            <td class="py-3 text-theme-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                            <td class="py-3 text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                                {{ $registration->user?->name ?? '-' }}
                                            </td>
                                            <td class="py-3 text-theme-sm text-gray-500 dark:text-gray-400">
                                                {{ $registration->user?->email ?? '-' }}
                                            </td>
                                            <td class="py-3 text-theme-sm text-gray-500 dark:text-gray-400">
                                                {{ $registration->created_at?->format('d M Y') }}
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
