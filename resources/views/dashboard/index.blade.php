@extends('layouts.app')

@section('content')
    @php
        $subscriptionStatus = $subscription?->status?->value ?? \App\SubscriptionStatus::Unpaid->value;
        $hasActiveSubscription = $user->hasActiveSubscription();

        $statusClasses = [
            'active' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'pending' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'expired' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            'unpaid' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];

        $eventStatusStyles = [
            'upcoming' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
            'ongoing' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'past' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            'cancelled' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
        ];
    @endphp

    @php
        $subscriptionStatusKey = 'ui.subscriptions.status.' . $subscriptionStatus;
        $subscriptionStatusLabel = trans()->has($subscriptionStatusKey)
            ? __($subscriptionStatusKey)
            : ucfirst($subscriptionStatus);
    @endphp

    <x-common.page-breadcrumb :pageTitle="__('ui.dashboard.title')" />

    @if (session('warning'))
        <x-ui.alert variant="warning" :title="__('ui.dashboard.access_limited')" :message="session('warning')" class="mb-6" />
    @endif

    @if (!$hasActiveSubscription)
        <x-ui.alert variant="warning" :title="__('ui.dashboard.subscription_required')" :message="__('ui.dashboard.subscription_required_message')" class="mb-6" />
    @endif

    <div class="grid gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.dashboard.welcome_back') }}</p>
                        <h2 class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $user->name }}</h2>
                        {{-- <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
		                            {{ __('ui.dashboard.member_status') }}
		                            <span class="ml-1 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$subscriptionStatus] ?? $statusClasses['unpaid'] }}">
		                                {{ $subscriptionStatusLabel }}
		                            </span>
		                        </p> --}}
                    </div>

                    <div class="grid w-full grid-cols-1 gap-3 sm:w-auto sm:grid-cols-3">
                        <div class="rounded-xl border border-gray-200 px-4 py-3 text-center dark:border-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.dashboard.unread_notifications') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                {{ $stats['unread_notifications'] }}</p>
                        </div>
                        {{-- <div class="rounded-xl border border-gray-200 px-4 py-3 text-center dark:border-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.dashboard.invoices') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                {{ $stats['total_invoices'] }}</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 px-4 py-3 text-center dark:border-gray-800">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.dashboard.pending_invoices') }}
                            </p>
                            <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">
                                {{ $stats['pending_invoices'] }}</p>
                        </div> --}}
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:px-6 sm:py-4 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="mb-3 flex items-center">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                        {{ __('ui.dashboard.upcoming_events') }}</h3>
                </div>

                @if ($upcomingEvents->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.dashboard.no_upcoming_events') }}</p>
                @else
	                    <div class="space-y-2.5">
	                        @foreach ($upcomingEvents as $event)
	                            @php
	                                $status = $event->status?->value ?? 'upcoming';
                                $eventStatusKey = 'ui.events.status.' . $status;
                                $eventStatusLabel = trans()->has($eventStatusKey)
                                    ? __($eventStatusKey)
                                    : ucfirst($status);
                                $isRegistered = (bool) ($event->is_registered ?? false);
                                $eventImage = $event->image;

                                if (
                                    $eventImage &&
                                    !\Illuminate\Support\Str::startsWith($eventImage, ['http://', 'https://', '/'])
                                ) {
                                    $eventImage = asset('storage/' . $eventImage);
                                }
                            @endphp
	                            <div
	                                class="task rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-white/5">
	                                <div class="flex flex-col gap-4 sm:flex-row sm:items-stretch">
	                                    <div
	                                        class="h-40 w-full shrink-0 self-stretch overflow-hidden rounded-xl border border-gray-200 sm:h-44 sm:w-36 dark:border-gray-800">
	                                        @if ($eventImage)
	                                            <img src="{{ $eventImage }}" alt="{{ $event->title }}"
	                                                class="h-full w-full object-cover">
                                        @else
                                            <div
                                                class="flex h-full w-full items-center justify-center bg-gray-100 dark:bg-gray-900/70">
                                                <img src="/images/logo/logo-dark.png" alt="AMUHI"
                                                    class="h-28 w-28 object-contain opacity-80 dark:brightness-0 dark:invert"
                                                    loading="lazy">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1 flex flex-col">
                                        <div>
                                            <div class="flex flex-wrap items-start justify-between gap-2">
                                                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                                                    {{ $event->title }}</h4>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $eventStatusStyles[$status] ?? $eventStatusStyles['upcoming'] }}">
                                                        {{ $eventStatusLabel }}
                                                    </span>
                                                    @if ($isRegistered)
                                                        <span
                                                            class="inline-flex rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                            {{ __('ui.dashboard.registered') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-2 space-y-1 text-xs text-gray-500 dark:text-gray-400">
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
                                                @if (($event->event_registrations_count ?? 0) >= 3)
                                                    <p class="flex items-center gap-2">
                                                        <span
                                                            class="text-gray-500 dark:text-gray-400 [&_svg]:h-4 [&_svg]:w-4">
                                                            {!! \App\Helpers\MenuHelper::getIconSvg('user-profile') !!}
                                                        </span>
                                                        {{ trans_choice('ui.dashboard.participants', $event->event_registrations_count ?? 0, ['count' => $event->event_registrations_count ?? 0]) }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-auto flex flex-wrap items-center gap-2 pt-3">
                                            <a href="{{ route('events.show', $event) }}"
                                                class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50 sm:w-auto dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                                                {{ __('ui.common.view_details') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 flex justify-end">
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        {{ __('ui.common.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="space-y-6 xl:col-span-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-2 text-base font-semibold text-gray-800 dark:text-white/90">
                    {{ __('ui.dashboard.recent_activity') }}</h3>

                @if ($recentActivity->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.dashboard.no_recent_activity') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($recentActivity as $activity)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ ucfirst($activity->action) }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $activity->description ?: __('ui.dashboard.action_recorded') }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">
                    {{ __('ui.dashboard.quick_actions') }}</h3>
                <div class="grid gap-3">
                    <a href="{{ route('profile.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-white/5">
                        {{ __('ui.dashboard.view_profile') }}
                    </a>
                    <a href="{{ route('settings.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-white/5">
                        {{ __('ui.common.open_settings') }}
                    </a>
                    <a href="{{ route('payments.show') }}"
                        class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-white/5">
                        {{ __('ui.common.manage_subscription') }}
                    </a>

                    <a href="{{ route('events.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium transition dark:border-gray-800 {{ $hasActiveSubscription ? 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' : 'cursor-not-allowed text-gray-400 dark:text-gray-500' }}">
                        {{ __('ui.dashboard.browse_events') }}
                    </a>
                    <a href="{{ route('programs.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium transition dark:border-gray-800 {{ $hasActiveSubscription ? 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' : 'cursor-not-allowed text-gray-400 dark:text-gray-500' }}">
                        {{ __('ui.dashboard.explore_programs') }}
                    </a>
                    <a href="{{ route('invoices.index') }}"
                        class="rounded-lg border border-gray-200 px-4 py-3 text-sm font-medium transition dark:border-gray-800 {{ $hasActiveSubscription ? 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-white/5' : 'cursor-not-allowed text-gray-400 dark:text-gray-500' }}">
                        {{ __('ui.dashboard.check_invoices') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
