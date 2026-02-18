@extends('admin.layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-title-sm font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.edit_admin') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Update role for {{ $admin->name }}.
                </p>
            </div>
            <a href="{{ route('admin.admins.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                Back to Admins
            </a>
        </div>

        @if ($errors->any())
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => 'Validation Error',
                'message' => $errors->first(),
            ])
        @endif

        @if (session('error'))
            @include('admin.components.alert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => session('error'),
            ])
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h2 class="mb-5 text-base font-semibold text-gray-800 dark:text-white/90">{{ __('ui.admin.admin_info') }}</h2>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.name') }}</span>
                            <span class="w-2/3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $admin->name }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.email') }}</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $admin->email }}</span>
                        </li>
                        <li class="flex items-start gap-5 py-2.5">
                            <span class="w-1/3 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.admin.joined') }}</span>
                            <span class="w-2/3 text-sm text-gray-700 dark:text-gray-300">{{ $admin->created_at?->format('d M Y') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-2">
                @component('admin.components.form-card', [
                    'title' => 'Update Role',
                    'description' => 'Change the admin\'s access level.',
                ])
                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Role
                                </label>
                                <div class="relative">
                                    <select name="role"
                                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        @foreach ($roles as $role)
                                            @php
                                                $currentRole = is_object($admin->role) ? $admin->role->value : $admin->role;
                                            @endphp
                                            <option value="{{ $role->value }}"
                                                {{ old('role', $currentRole) === $role->value ? 'selected' : '' }}
                                                class="dark:bg-gray-900">
                                                {{ ucwords(str_replace('_', ' ', $role->value)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                @error('role')
                                    <p class="mt-1 text-theme-xs text-error-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600">
                                    Update Role
                                </button>
                                <a href="{{ route('admin.admins.index') }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                @endcomponent
            </div>
        </div>
    </div>
@endsection
