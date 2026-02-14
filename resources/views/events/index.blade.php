@extends('layouts.app')

@section('content')
    @php
        $events = $allEvents;

        $statusStyles = [
            'upcoming' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
            'ongoing' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'past' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'cancelled' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];

        $upcomingCount = $events->filter(fn ($event) => $event->status?->value === \App\EventStatus::Upcoming->value)->count();
        $ongoingCount = $events->filter(fn ($event) => $event->status?->value === \App\EventStatus::Ongoing->value)->count();
        $pastCount = $events->filter(fn ($event) => $event->status?->value === \App\EventStatus::Past->value)->count();
        $registeredCount = $registeredEvents->count();
    @endphp

	    <x-common.page-breadcrumb :pageTitle="__('ui.events.title')" />

	    @if (session('success'))
	        <x-ui.alert variant="success" :title="__('ui.common.success')" :message="session('success')" class="mb-6" />
	    @endif

	    @if (session('warning'))
	        <x-ui.alert variant="warning" :title="__('ui.common.notice')" :message="session('warning')" class="mb-6" />
	    @endif

	    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
	        <div class="mb-6 flex items-center justify-between">
	            <div>
	                <h2 class="font-semibold text-gray-800 dark:text-white/90">{{ __('ui.common.overview') }}</h2>
	            </div>
            {{-- <div>
                <a href="{{ route('events.calendar') }}"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Calendar View
                </a>
            </div> --}}
        </div>

	        <div
	            class="grid grid-cols-1 rounded-xl border border-gray-200 sm:grid-cols-2 lg:grid-cols-4 lg:divide-x lg:divide-y-0 dark:divide-gray-800 dark:border-gray-800">
	            <div class="border-b p-5 sm:border-r lg:border-b-0">
	                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">{{ __('ui.events.upcoming') }}</p>
	                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $upcomingCount }}</h3>
	            </div>
	            <div class="border-b p-5 lg:border-b-0">
	                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">{{ __('ui.events.ongoing') }}</p>
	                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $ongoingCount }}</h3>
	            </div>
	            <div class="border-b p-5 sm:border-r sm:border-b-0">
	                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">{{ __('ui.events.past') }}</p>
	                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $pastCount }}</h3>
	            </div>
	            <div class="p-5">
	                <p class="mb-1.5 text-sm text-gray-400 dark:text-gray-500">{{ __('ui.events.registered_events') }}</p>
	                <h3 class="text-3xl text-gray-800 dark:text-white/90">{{ $registeredCount }}</h3>
	            </div>
	        </div>
	    </div>

    <div class="grid gap-6 xl:grid-cols-12">
	        <div class="space-y-6 xl:col-span-8">
	            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
	                <div class="mb-4 flex items-center justify-between">
	                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.events.event_list') }}</h3>
	                    {{-- <a href="{{ route('events.calendar') }}" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">Calendar view</a> --}}
	                </div>

	                @if ($events->isEmpty())
	                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.events.no_events_published') }}</p>
	                @else
	                    <div class="space-y-4">
	                        @foreach ($events as $event)
	                            @php
	                                $status = $event->status?->value ?? 'upcoming';
	                                $statusKey = 'ui.events.status.'.$status;
	                                $statusLabel = trans()->has($statusKey) ? __($statusKey) : ucfirst($status);
	                                $isRegistered = (bool) ($event->is_registered ?? false);
	                                $canRegister = ! in_array($status, ['past', 'cancelled'], true);
	                                $eventImage = $event->image;

                                if ($eventImage && ! \Illuminate\Support\Str::startsWith($eventImage, ['http://', 'https://', '/'])) {
                                    $eventImage = asset('storage/' . $eventImage);
                                }
                            @endphp
                            <div class="task rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm transition-all hover:shadow-lg dark:border-gray-800 dark:bg-white/5">
                                <div class="flex items-start gap-4">
                                    @if ($eventImage)
                                        <div class="h-44 w-52 shrink-0 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                                            <img src="{{ $eventImage }}" alt="{{ $event->title }}" class="h-full w-full object-cover">
                                        </div>
                                    @endif

	                                    <div class="min-w-0 flex-1">
	                                        <div class="flex flex-wrap items-start justify-between gap-3">
	                                            <h4 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $event->title }}</h4>
	                                            <div class="flex items-center gap-2">
	                                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$status] ?? $statusStyles['upcoming'] }}">
	                                                    {{ $statusLabel }}
	                                                </span>
	                                                @if ($isRegistered)
	                                                    <span class="inline-flex rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
	                                                        {{ __('ui.dashboard.registered') }}
	                                                    </span>
	                                                @endif
	                                            </div>
	                                        </div>

                                        <div class="mt-2 space-y-1.5 text-sm text-gray-500 dark:text-gray-400">
	                                            <p class="flex items-center gap-2">
                                                <span class="text-gray-500 dark:text-gray-400 [&_svg]:h-4 [&_svg]:w-4">
                                                    {!! \App\Helpers\MenuHelper::getIconSvg('calendar') !!}
                                                </span>
	                                                {{ optional($event->starts_at)->format('d M Y, H:i') ?? __('ui.common.tba') }}
	                                                @if ($event->ends_at)
	                                                    - {{ $event->ends_at->format('d M Y, H:i') }}
	                                                @endif
	                                            </p>

	                                            <p class="flex items-center gap-2">
                                                <span class="text-gray-500 dark:text-gray-400 [&_svg]:h-4 [&_svg]:w-4">
                                                    {!! \App\Helpers\MenuHelper::getIconSvg('pages') !!}
                                                </span>
	                                                {{ $event->location ?: __('ui.common.tba') }}
	                                            </p>

	                                            @if ($event->registrations_count >= 3)
                                                    <p class="flex items-center gap-2">
                                                    <span class="text-gray-500 dark:text-gray-400 [&_svg]:h-4 [&_svg]:w-4">
                                                        {!! \App\Helpers\MenuHelper::getIconSvg('user-profile') !!}
                                                    </span>
	                                                    {{ trans_choice('ui.dashboard.participants', $event->registrations_count, ['count' => $event->registrations_count]) }}
	                                                </p>
                                                @endif
	                                        </div>
	                                    </div>
	                                </div>

                                @if ($event->description)
                                    <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ $event->description }}</p>
                                @endif

	                                <div class="mt-4 flex flex-wrap items-center gap-2">
	                                    <a href="{{ route('events.show', $event) }}"
	                                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
	                                        {{ __('ui.common.view_details') }}
	                                    </a>

	                                    @if ($isRegistered)
	                                        <button type="button"
	                                            class="inline-flex cursor-default items-center justify-center rounded-lg border border-success-300 px-4 py-2 text-sm font-medium text-success-600 dark:border-success-500/40 dark:text-success-400">
	                                            {{ __('ui.events.you_are_registered') }}
	                                        </button>
	                                    @elseif ($canRegister)
	                                        <form method="POST" action="{{ route('events.register', $event) }}">
	                                            @csrf
	                                            <button type="submit"
	                                                class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-600">
	                                                {{ __('ui.events.register') }}
	                                            </button>
	                                        </form>
	                                    @else
	                                        <button type="button"
	                                            class="inline-flex cursor-not-allowed items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-500 dark:border-gray-700 dark:text-gray-400">
	                                            {{ __('ui.events.registration_closed') }}
	                                        </button>
	                                    @endif
	                                </div>
	                            </div>
	                        @endforeach
	                    </div>
	                @endif
	            </div>
	        </div>

	        <div class="space-y-6 xl:col-span-4">
	            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
	                <h3 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.events.my_registered_events') }}</h3>

	                @if ($registeredEvents->isEmpty())
	                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.events.no_registered_events') }}</p>
	                @else
	                    <div class="space-y-3">
	                        @foreach ($registeredEvents as $event)
                            @php
                                $eventImage = $event->image;

                                if ($eventImage && ! \Illuminate\Support\Str::startsWith($eventImage, ['http://', 'https://', '/'])) {
                                    $eventImage = asset('storage/' . $eventImage);
                                }
                            @endphp
                            <a href="{{ route('events.show', $event) }}"
                                class="task block rounded-xl border border-gray-200 bg-white p-5 shadow-theme-sm transition-all hover:shadow-lg dark:border-gray-800 dark:bg-white/5">
                                <div class="flex items-start gap-3">
                                    @if ($eventImage)
                                        <img src="{{ $eventImage }}" alt="{{ $event->title }}"
                                            class="h-12 w-16 rounded-lg border border-gray-200 object-cover dark:border-gray-800">
                                    @endif

	                                    <div class="min-w-0">
	                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $event->title }}</p>
	                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
	                                            {{ optional($event->starts_at)->format('d M Y, H:i') ?? __('ui.common.tba') }}
	                                        </p>
	                                    </div>
	                                </div>
	                            </a>
	                        @endforeach
	                    </div>
	                @endif
	            </div>
	        </div>
	    </div>
@endsection
