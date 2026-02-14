@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        :pageTitle="__('ui.events.calendar_title')"
        :items="[
            ['label' => __('ui.events.title'), 'href' => route('events.index')],
            ['label' => __('ui.events.calendar_title')],
        ]"
    />

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.events.calendar_view') }}</h3>
                <a href="{{ route('events.index') }}" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">{{ __('ui.events.back_to_list') }}</a>
            </div>
            <x-calender-area />
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.events.upcoming_schedule') }}</h3>

            @if ($events->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.events.no_scheduled_events') }}</p>
            @else
                <div class="space-y-3">
                    @foreach ($events as $event)
                        @php
                            $eventImage = $event->image;

                            if ($eventImage && ! \Illuminate\Support\Str::startsWith($eventImage, ['http://', 'https://', '/'])) {
                                $eventImage = asset('storage/' . $eventImage);
                            }
                        @endphp
                        <a href="{{ route('events.show', $event) }}" class="block rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/5">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <div class="flex min-w-0 flex-1 items-start gap-3">
                                    @if ($eventImage)
                                        <img src="{{ $eventImage }}" alt="{{ $event->title }}"
                                            class="h-12 w-16 rounded-lg border border-gray-200 object-cover dark:border-gray-800">
                                    @endif

                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $event->title }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ optional($event->starts_at)->format('d M Y, H:i') ?? __('ui.common.tba') }}
                                            @if ($event->location)
                                                • {{ $event->location }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if (($event->is_registered ?? false) === true)
                                    <span class="inline-flex rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                        {{ __('ui.dashboard.registered') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
