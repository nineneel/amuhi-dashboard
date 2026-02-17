@php
    use App\Enums\Role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Admin Management</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Super admin area for viewing existing admin and super admin accounts.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.admins.create') }}"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                    Add Admin
                </a>
                <button type="button"
                    class="inline-flex items-center rounded-lg bg-gray-700 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-gray-600"
                    @click="$dispatch('open-modal-admin-permissions')">
                    View Access Rules
                </button>
            </div>
        </div>

        @if (session('warning'))
            @include('admin.components.alert', [
                'type' => 'warning',
                'title' => 'Notice',
                'message' => session('warning'),
            ])
        @endif

        @if (session('status'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('status'),
            ])
        @endif

        @component('admin.components.form-card', [
            'title' => 'Filters',
            'description' => 'Search and filter the admin directory.',
        ])
            @include('admin.components.filters', [
                'action' => route('admin.admins.index'),
                'searchPlaceholder' => 'Search by name or email',
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
                ],
            ])
        @endcomponent

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Name', 'key' => 'name', 'sortable' => true],
                ['label' => 'Email', 'key' => 'email', 'sortable' => true],
                ['label' => 'Role', 'key' => 'role', 'sortable' => true],
                ['label' => 'Created', 'key' => 'created_at', 'sortable' => true],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
            'sortable' => true,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ])
            @forelse ($admins as $admin)
                @php
                    $roleValue = $admin->role instanceof Role ? $admin->role->value : (string) $admin->role;
                    $roleLabel = ucwords(str_replace('_', ' ', $roleValue));
                    $roleBadgeType = $roleValue === Role::SuperAdmin->value ? 'info' : 'neutral';
                @endphp

                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <div>
                            <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $admin->name }}</p>
                            <p class="text-theme-xs text-gray-500 dark:text-gray-400">ID: {{ $admin->id }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $admin->email }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.badge', ['type' => $roleBadgeType, 'text' => $roleLabel])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $admin->created_at?->format('d M Y') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.admins.edit', $admin) }}"
                                class="rounded-lg bg-gray-700 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-gray-600">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No admin users found for the selected filters.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $admins])
    </div>

    @component('admin.components.modal', [
        'id' => 'admin-permissions',
        'title' => 'Admin Access Rules',
        'size' => 'md',
    ])
        <p>
            Super Admin can access the <strong>Admins</strong> menu and manage administrator accounts.
        </p>

        <p>
            Standard Admin can access CMS and portal management menus, but cannot access admin account management.
        </p>
    @endcomponent
@endsection
