@extends('layouts.app')

@section('content')
    @php
        $programs = [
            'AMUHI Academy',
            'AMUHI Check',
            'AMUHI Protect',
            'AMUHI Care',
            'AMUHI Network',
            'AMUHI Digital',
        ];
    @endphp

    <x-common.page-breadcrumb :pageTitle="__('ui.programs.title')" />

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] lg:p-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">{{ __('ui.programs.count_title', ['count' => count($programs)]) }}</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.programs.subtitle') }}
                </p>
            </div>
            <span class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                {{ __('ui.programs.coming_soon') }}
            </span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($programs as $program)
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $program }}</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('ui.programs.module_under_development') }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
