@extends('layouts.app')

@section('content')
    @php
        $forumModules = [
            __('ui.forum.modules.categories'),
            __('ui.forum.modules.discussions'),
            __('ui.forum.modules.member_qa'),
            __('ui.forum.modules.announcements'),
        ];
    @endphp

    <x-common.page-breadcrumb :pageTitle="__('ui.forum.title')" />

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] lg:p-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                    {{ __('ui.forum.count_title', ['count' => count($forumModules)]) }}
                </h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.forum.subtitle') }}
                </p>
            </div>
            <span class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                {{ __('ui.forum.coming_soon') }}
            </span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($forumModules as $module)
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $module }}</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('ui.forum.module_under_development') }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
@endsection

