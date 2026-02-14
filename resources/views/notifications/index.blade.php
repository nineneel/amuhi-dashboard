@extends('layouts.app')

@section('content')
    @php
        $unreadCount = $notifications->whereNull('read_at')->count();
    @endphp

    <x-common.page-breadcrumb :pageTitle="__('ui.notifications.title')" />

    @if (session('success'))
        <x-ui.alert variant="success" :title="__('ui.common.success')" :message="session('success')" class="mb-6" />
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.notifications.all') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.notifications.summary', ['total' => $notifications->count(), 'unread' => $unreadCount]) }}</p>
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                        {{ __('ui.notifications.mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>

        @if ($notifications->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.notifications.empty') }}</p>
        @else
            <div class="space-y-3">
                @foreach ($notifications as $notification)
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800 {{ $notification->read_at ? '' : 'bg-brand-50/30 dark:bg-brand-500/5' }}">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $notification->title }}</p>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $notification->message }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ ucfirst($notification->type) }} • {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($notification->read_at)
                                    <span class="inline-flex rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                        {{ __('ui.notifications.read') }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-brand-600">
                                            {{ __('ui.notifications.mark_read') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
