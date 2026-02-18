@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.users') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.member_directory_for_portal_users') }}</p>
            </div>
        </div>

        @if (session('success'))
            @include('admin.components.alert', [
                'type' => 'success',
                'title' => 'Success',
                'message' => session('success'),
            ])
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            @include('admin.components.filters', [
                'action' => route('admin.users.index'),
                'searchPlaceholder' => 'Search members by name or email',
                'showDateRange' => false,
                'filters' => [
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
                'class' => 'grid-cols-1 md:grid-cols-4',
            ])
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Member'],
                ['label' => 'Joined'],
                ['label' => 'Actions'],
            ],
            'paginator' => $users,
        ])
            @forelse ($users as $user)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.avatar-cell', [
                            'name' => $user->name,
                            'subtitle' => $user->email,
                        ])
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at?->format('d M Y') }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        @include('admin.components.table-action-icons', [
                            'viewUrl' => route('admin.users.show', $user),
                        ])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.no_member_users_found') }}</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
