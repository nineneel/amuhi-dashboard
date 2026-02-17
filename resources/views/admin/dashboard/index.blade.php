@php
    use App\Helpers\AdminMenuHelper;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Admin Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Overview of portal activity and CMS performance.
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

        <div class="grid grid-cols-1 gap-4 md:gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($metrics as $metric)
                @include('admin.components.stats-card', [
                    'title' => $metric['title'],
                    'value' => $metric['value'],
                    'icon' => AdminMenuHelper::getIconSvg($metric['icon']),
                    'trend' => $metric['trend'],
                ])
            @endforeach
        </div>

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Action', 'key' => 'action'],
                ['label' => 'User', 'key' => 'user'],
                ['label' => 'Description', 'key' => 'description'],
                ['label' => 'Time', 'key' => 'created_at'],
            ],
        ])
            @forelse ($recentActivities as $activity)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $activity->action }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $activity->user?->email ?? 'System' }}</p>
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
                    <td colspan="4" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No recent activity yet.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent
    </div>
@endsection
