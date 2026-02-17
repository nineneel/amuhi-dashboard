@php
    use App\Enums\Role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Users</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage portal users and account roles.</p>
            </div>
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

        @component('admin.components.form-card', [
            'title' => 'Filters',
            'description' => 'Search and filter users by role or status.',
        ])
            @include('admin.components.filters', [
                'action' => route('admin.users.index'),
                'searchPlaceholder' => 'Search users by name or email',
                'showDateRange' => false,
                'filters' => [
                    [
                        'name' => 'role',
                        'label' => 'Role',
                        'options' => [
                            Role::Member->value => 'Member',
                            Role::Admin->value => 'Admin',
                            Role::SuperAdmin->value => 'Super Admin',
                        ],
                    ],
                    [
                        'name' => 'status',
                        'label' => 'Status',
                        'options' => [
                            'verified' => 'Verified',
                            'unverified' => 'Unverified',
                        ],
                    ],
                ],
            ])
        @endcomponent

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Name', 'key' => 'name', 'sortable' => true],
                ['label' => 'Email', 'key' => 'email', 'sortable' => true],
                ['label' => 'Role', 'key' => 'role'],
                ['label' => 'Subs', 'key' => 'subscriptions_count'],
                ['label' => 'Invoices', 'key' => 'invoices_count'],
                ['label' => 'Created', 'key' => 'created_at', 'sortable' => true],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
            'sortable' => true,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ])
            @forelse ($users as $user)
                @php
                    $roleValue = $user->role instanceof Role ? $user->role->value : (string) $user->role;
                    $roleLabel = ucwords(str_replace('_', ' ', $roleValue));
                @endphp
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $roleLabel }}</p>
                    </td>
                    <td class="px-5 py-4 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $user->subscriptions_count }}</p>
                    </td>
                    <td class="px-5 py-4 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $user->invoices_count }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->format('d M Y') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.users.show', $user) }}"
                                class="rounded-lg bg-brand-500 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-brand-600">View</a>
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="rounded-lg bg-gray-700 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-gray-600">Edit</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No users found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $users])
    </div>
@endsection
