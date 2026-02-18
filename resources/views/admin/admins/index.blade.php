@php
    use App\Enums\Role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Admin Management</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage admin and super admin accounts.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.admins.create') }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
                    Add Admin
                </a>
                <button type="button"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                    @click="$dispatch('open-modal-admin-permissions')">
                    Access Rules
                </button>
            </div>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        @if (session('error'))
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => session('error'),
            ])
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.admins.index'),
                'searchPlaceholder' => 'Search admin by name or email',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'role',
                        'label' => 'Role',
                        'options' => [
                            Role::Admin->value => 'Admin',
                            Role::SuperAdmin->value => 'Super Admin',
                        ],
                    ],
                    [
                        'name' => 'per_page',
                        'label' => 'Rows',
                        'include_all_option' => false,
                        'options' => [
                            '10' => '10 rows',
                            '15' => '15 rows',
                            '25' => '25 rows',
                            '50' => '50 rows',
                        ],
                        'value' => (string) request('per_page', '15'),
                    ],
                ],
                'class' => 'grid-cols-1 md:grid-cols-5',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Admin'],
                ['label' => 'Role', 'key' => 'role', 'sortable' => true],
                ['label' => 'Joined', 'key' => 'created_at', 'sortable' => true],
                ['label' => 'Actions'],
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
                            'deleteConfirm' => 'Remove admin access for this user?',
                            'deleteLabel' => 'Remove admin access',
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No admin users found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>

    @component('admin.components.modal', [
        'id' => 'admin-permissions',
        'title' => 'Admin Access Rules',
        'size' => 'md',
    ])
        <p>Super Admin can access the <strong>Admins</strong> menu and manage administrator accounts.</p>

        <p>Standard Admin can access CMS and portal management menus, but cannot access admin account management.</p>
    @endcomponent
@endsection
