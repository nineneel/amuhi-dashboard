@php
    use App\Enums\Role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    @php
        $roleValue = $user->role instanceof Role ? $user->role->value : (string) $user->role;
        $roleLabel = ucwords(str_replace('_', ' ', $roleValue));
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">User Details</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
            </div>

            <a href="{{ route('admin.users.edit', $user) }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Edit User</a>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Role</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $roleLabel }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Subscriptions</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $user->subscriptions->count() }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email Verified</dt>
                    <dd class="mt-1 font-medium text-gray-800 dark:text-white/90">{{ $user->email_verified_at ? 'Yes' : 'No' }}</dd>
                </div>
            </dl>
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Action', 'key' => 'action'],
                ['label' => 'Description', 'key' => 'description'],
                ['label' => 'Time', 'key' => 'created_at'],
            ],
        ])
            @forelse ($user->activityLogs as $activity)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $activity->action }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $activity->description ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $activity->created_at?->format('d M Y H:i') }}</p>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No activity history found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
