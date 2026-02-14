@extends('layouts.app')

@section('content')
	    @php
	        $status = $event->status?->value ?? 'upcoming';
	        $statusKey = 'ui.events.status.'.$status;
	        $statusLabel = trans()->has($statusKey) ? __($statusKey) : ucfirst($status);
	        $statusStyles = [
	            'upcoming' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
	            'ongoing' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
	            'past' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'cancelled' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];
        $canRegister = ! in_array($status, ['past', 'cancelled'], true);
        $eventImage = $event->image;

        if ($eventImage && ! \Illuminate\Support\Str::startsWith($eventImage, ['http://', 'https://', '/'])) {
            $eventImage = asset('storage/' . $eventImage);
        }
    @endphp

	    <x-common.page-breadcrumb
	        :pageTitle="__('ui.events.event_details_title')"
	        :items="[
	            ['label' => __('ui.events.title'), 'href' => route('events.index')],
	            ['label' => __('ui.events.event_details_title')],
	        ]"
	    />

	    @if (session('success'))
	        <x-ui.alert variant="success" :title="__('ui.common.success')" :message="session('success')" class="mb-6" />
	    @endif

	    @if (session('warning'))
	        <x-ui.alert variant="warning" :title="__('ui.common.notice')" :message="session('warning')" class="mb-6" />
	    @endif

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
	                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
	                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $event->title }}</h2>
	                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusStyles[$status] ?? $statusStyles['upcoming'] }}">
	                        {{ $statusLabel }}
	                    </span>
	                </div>

                @if ($eventImage)
                    <div class="mb-4">
                        <img src="{{ $eventImage }}" alt="{{ $event->title }}"
                            class="h-52 w-full rounded-xl border border-gray-200 object-cover dark:border-gray-800">
                    </div>
                @endif

	                <div class="grid gap-4 sm:grid-cols-2">
	                    <div>
	                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.events.starts_at') }}</p>
	                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ optional($event->starts_at)->format('d M Y, H:i') ?? __('ui.common.tba') }}</p>
	                    </div>
	                    <div>
	                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.events.ends_at') }}</p>
	                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ optional($event->ends_at)->format('d M Y, H:i') ?? __('ui.common.tba') }}</p>
	                    </div>
	                    <div class="sm:col-span-2">
	                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.events.location') }}</p>
	                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $event->location ?: __('ui.common.tba') }}</p>
	                    </div>
	                    <div class="sm:col-span-2">
	                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.events.description') }}</p>
	                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $event->description ?: __('ui.events.no_description') }}</p>
	                    </div>
	                </div>
	            </div>
	        </div>

	        <div class="space-y-6 xl:col-span-4">
	            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
	                <h3 class="mb-3 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.events.registration') }}</h3>
	                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
	                    {{ trans_choice('ui.events.registered_members', $event->event_registrations_count ?? 0, ['count' => $event->event_registrations_count ?? 0]) }}
	                </p>

	                @if ($isRegistered)
	                    <button type="button"
	                        class="inline-flex w-full cursor-default items-center justify-center rounded-lg border border-success-300 px-4 py-2.5 text-sm font-medium text-success-600 dark:border-success-500/40 dark:text-success-400">
	                        {{ __('ui.events.you_are_registered') }}
	                    </button>
	                @elseif ($canRegister)
	                    <form method="POST" action="{{ route('events.register', $event) }}">
	                        @csrf
	                        <button type="submit"
	                            class="inline-flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
	                            {{ __('ui.events.register_for_event') }}
	                        </button>
	                    </form>
	                @else
	                    <button type="button"
	                        class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-500 dark:border-gray-700 dark:text-gray-400">
	                        {{ __('ui.events.registration_closed') }}
	                    </button>
	                @endif

	                <a href="{{ route('events.index') }}"
	                    class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
	                    {{ __('ui.events.back_to_events') }}
	                </a>
	            </div>
	        </div>
	    </div>
@endsection
