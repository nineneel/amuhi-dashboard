@php
    use App\Enums\Role;

    $roleValue = $admin->role instanceof Role ? $admin->role->value : (string) $admin->role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Edit Admin Role</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $admin->email }}</p>
        </div>

        <form action="{{ route('admin.admins.update', $admin) }}" method="POST"
            class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            @csrf
            @method('PUT')

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select name="role"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90">
                    <option value="{{ Role::Admin->value }}" @selected(old('role', $roleValue) === Role::Admin->value)>Admin</option>
                    <option value="{{ Role::SuperAdmin->value }}" @selected(old('role', $roleValue) === Role::SuperAdmin->value)>Super Admin</option>
                </select>
                @error('role')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap justify-between gap-2">
                <a href="{{ route('admin.admins.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Back</a>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Update Role
                </button>
            </div>
        </form>

        <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
            onsubmit="return confirm('Remove admin access for this account?');"
            class="rounded-2xl border border-error-200 bg-error-50 p-6 dark:border-error-700 dark:bg-error-900/10">
            @csrf
            @method('DELETE')

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-error-700 dark:text-error-400">Remove Admin Access</h2>
                    <p class="mt-1 text-theme-sm text-error-600/90 dark:text-error-400/90">This account will become a member.</p>
                </div>

                <button type="submit" class="rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600">
                    Remove Access
                </button>
            </div>
        </form>
    </div>
@endsection
