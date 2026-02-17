@php
    use App\Helpers\AdminMenuHelper;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Overview of portal activity and CMS navigation.
            </p>
        </div>

        @if (session('status'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('status'),
            ])
        @endif

        @if (session('warning'))
            @include('admin.components.alert', [
                'type' => 'warning',
                'title' => 'Notice',
                'message' => session('warning'),
            ])
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 md:gap-6">
            @foreach ($metrics as $metric)
                @include('admin.components.stats-card', [
                    'title' => $metric['title'],
                    'value' => $metric['value'],
                    'icon' => AdminMenuHelper::getIconSvg($metric['icon']),
                    'trend' => $metric['trend'],
                ])
            @endforeach
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-800 dark:text-white/90">Quick Actions</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Go back to portal or review admin management.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                        class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
                        @click="$dispatch('open-modal-admin-overview')">
                        Open Overview Modal
                    </button>
                </div>
            </div>
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Section', 'key' => 'section'],
                ['label' => 'Description', 'key' => 'description'],
                ['label' => 'Status', 'key' => 'status'],
            ],
        ])
            <tr class="border-b border-gray-100 dark:border-gray-800">
                <td class="px-5 py-4 sm:px-6">
                    <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Admin Navigation</p>
                </td>
                <td class="px-5 py-4 sm:px-6">
                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">Sidebar and header are now role-aware.</p>
                </td>
                <td class="px-5 py-4 sm:px-6">
                    @include('admin.components.badge', ['type' => 'success', 'text' => 'Completed'])
                </td>
            </tr>
            <tr class="border-b border-gray-100 dark:border-gray-800">
                <td class="px-5 py-4 sm:px-6">
                    <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Reusable Components</p>
                </td>
                <td class="px-5 py-4 sm:px-6">
                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">Stats cards, forms, filters, table, pagination, modal, alerts.</p>
                </td>
                <td class="px-5 py-4 sm:px-6">
                    @include('admin.components.badge', ['type' => 'success', 'text' => 'Completed'])
                </td>
            </tr>
        @endcomponent
    </div>

    @component('admin.components.modal', [
        'id' => 'admin-overview',
        'title' => 'Admin Overview',
        'size' => 'md',
    ])
        <p>
            The admin layout and reusable components are active. Use the sidebar to navigate and the Admin menu is only visible to super administrators.
        </p>

        <p>
            Next phases will attach CRUD screens and CMS data models to this interface.
        </p>
    @endcomponent
@endsection
