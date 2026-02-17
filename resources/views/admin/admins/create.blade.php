@php
    use App\Enums\Role;
@endphp

@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">Create Admin</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create a new admin or super admin account.</p>
        </div>

        <form action="{{ route('admin.admins.store') }}" method="POST"
            class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            @csrf

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                <input name="name" type="text" value="{{ old('name') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                @error('name')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input name="email" type="email" value="{{ old('email') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                @error('email')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input name="password" type="password"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                @error('password')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                <select name="role"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90">
                    <option value="{{ Role::Admin->value }}" @selected(old('role') === Role::Admin->value)>Admin</option>
                    <option value="{{ Role::SuperAdmin->value }}" @selected(old('role') === Role::SuperAdmin->value)>Super Admin</option>
                </select>
                @error('role')
                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap justify-end gap-2">
                <a href="{{ route('admin.admins.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">Cancel</a>
                <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                    Create Admin
                </button>
            </div>
        </form>
    </div>
@endsection
