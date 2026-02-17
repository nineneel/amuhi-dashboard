@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Testimonies</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage homepage testimonies and sort order.</p>
            </div>

            <a href="{{ route('admin.testimonies.create') }}"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Create
                Testimony</a>
        </div>

        @if (session('status'))
            @include('admin.components.alert', ['type' => 'success', 'title' => 'Success', 'message' => session('status')])
        @endif

        @component('admin.components.data-table', [
            'headers' => [
                ['label' => 'Sort', 'key' => 'sort_order'],
                ['label' => 'Name', 'key' => 'name'],
                ['label' => 'Role', 'key' => 'role'],
                ['label' => 'Active', 'key' => 'is_active'],
                ['label' => 'Actions', 'key' => 'actions'],
            ],
        ])
            @forelse ($testimonies as $testimony)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 sm:px-6">{{ $testimony->sort_order }}</td>
                    <td class="px-5 py-4 sm:px-6">
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $testimony->name }}</p>
                        <p class="text-theme-xs text-gray-500 dark:text-gray-400">{{ $testimony->text }}</p>
                    </td>
                    <td class="px-5 py-4 sm:px-6">{{ $testimony->role }}</td>
                    <td class="px-5 py-4 sm:px-6">{{ $testimony->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-5 py-4 sm:px-6">
                        <a href="{{ route('admin.testimonies.edit', $testimony) }}"
                            class="rounded-lg bg-gray-700 px-3 py-1.5 text-theme-xs font-medium text-white hover:bg-gray-600">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center sm:px-6">
                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">No testimonies found.</p>
                    </td>
                </tr>
            @endforelse
        @endcomponent

        @include('admin.components.pagination', ['paginator' => $testimonies])
    </div>
@endsection
