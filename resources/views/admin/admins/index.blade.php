@php
    use App\Enums\Role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.admin_management') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.manage_admin_and_super_admin_accounts') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.admins.create') }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                    {{ __('ui.admin.add_admin') }}
                </a>
                <button type="button"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                    @click="$dispatch('open-modal-admin-permissions')">
                    {{ __('ui.admin.admin_access_rules') }}
                </button>
            </div>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => __('ui.admin.success'),
                'message' => session('success'),
            ])
        @endif

        @if (session('error'))
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => __('ui.admin.error'),
                'message' => session('error'),
            ])
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.admins.index'),
                'searchPlaceholder' => 'ui.admin.search_admin_by_name_or_email',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'role',
                        'label' => 'ui.admin.role',
                        'options' => [
                            Role::Admin->value => 'ui.admin.admin',
                            Role::SuperAdmin->value => 'ui.admin.super_admin',
                        ],
                    ],
                    [
                        'name' => 'per_page',
                        'label' => 'ui.admin.rows',
                        'include_all_option' => false,
                        'options' => [
                            '10' => 'ui.admin.rows_10',
                            '15' => 'ui.admin.rows_15',
                            '25' => 'ui.admin.rows_25',
                            '50' => 'ui.admin.rows_50',
                        ],
                        'value' => (string) request('per_page', '15'),
                    ],
                ],
                'class' => 'grid-cols-1 md:grid-cols-5',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'ui.admin.admin'],
                ['label' => 'ui.admin.role', 'key' => 'role', 'sortable' => true],
                ['label' => 'ui.admin.joined', 'key' => 'created_at', 'sortable' => true],
                ['label' => 'ui.admin.actions'],
            ],
            'sortable' => true,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'paginator' => $admins,
        ])
            @forelse ($admins as $admin)
                @php
                    $roleValue = $admin->role instanceof Role ? $admin->role->value : (string) $admin->role;
                    $roleLabel = ucwords(str_replace('_', ' ', $roleValue));
                    $roleBadgeType = $roleValue === Role::SuperAdmin->value ? 'info' : 'neutral';
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $admin->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $admin->email }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $roleBadgeType, 'text' => $roleLabel])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $admin->created_at?->format('d M Y') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'editUrl' => route('admin.admins.edit', $admin),
                            'deleteUrl' => route('admin.admins.destroy', $admin),
                            'deleteConfirm' => __('ui.admin.remove_admin_access_for_this_user'),
                            'deleteLabel' => __('ui.admin.remove_admin_access'),
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_admin_users_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>

    @component('admin.components.modal', [
        'id' => 'admin-permissions',
        'title' => __('ui.admin.admin_access_rules'),
        'size' => 'md',
    ])
        <p>{{ __('ui.admin.super_admin_can_access_the') }} <strong>{{ __('ui.admin.admins') }}</strong> {{ __('ui.admin.menu_and_manage_administrator_accounts') }}</p>

        <p>{{ __('ui.admin.standard_admin_can_access_cms_and_portal_management_menus_but_cannot_access_admin_account_management') }}</p>
    @endcomponent
@endsection
