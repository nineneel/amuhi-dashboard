@php
    use App\Enums\Role;

    $roleValue = $user->role instanceof Role ? $user->role->value : (string) $user->role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Edit User</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update user profile and role.</p>
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

        <form action="{{ route('admin.users.update', $user) }}" method="POST"
            class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input name="name" type="text" value="{{ old('name', $user->name) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                @error('name')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                @error('email')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select name="role"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90">
                    @foreach (Role::cases() as $role)
                        <option value="{{ $role->value }}" @selected(old('role', $roleValue) === $role->value)>
                            {{ ucwords(str_replace('_', ' ', $role->value)) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('admin.users.show', $user) }}"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Back</a>

                <div class="flex flex-wrap gap-2">
                    <button type="submit"
                        class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600">Save
                        Changes</button>
                </div>
            </div>
        </form>

        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
            onsubmit="return confirm('Delete this user account?');"
            class="rounded-2xl border border-error-200 bg-error-50 p-6 dark:border-error-700 dark:bg-error-900/10">
            @csrf
            @method('DELETE')

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-error-700 dark:text-error-400">Delete User</h2>
                    <p class="mt-1 text-theme-sm text-error-600/90 dark:text-error-400/90">This action cannot be undone.</p>
                </div>

                <button type="submit" class="rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600">
                    Delete User
                </button>
            </div>
        </form>
    </div>
@endsection
