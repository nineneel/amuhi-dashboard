@php
    use App\Enums\Role;

    $authUser = auth()->user();

    $roleValue = $authUser?->role instanceof Role
        ? $authUser->role->value
        : (string) ($authUser?->role ?? 'admin');

    $roleLabel = str_replace('_', ' ', $roleValue);
    $roleLabel = __(ucwords($roleLabel));

    $roleBadgeType = $roleValue === Role::SuperAdmin->value ? 'info' : 'neutral';
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <button class="flex items-center text-gray-700 dark:text-gray-400" @click.prevent="toggleDropdown()" type="button">
        <span
            class="mr-3 flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
            {{ strtoupper(substr($authUser?->name ?? 'A', 0, 1)) }}
        </span>

        <span class="mr-1 block text-theme-sm font-medium">{{ $authUser?->name }}</span>

        <svg class="h-5 w-5 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-[17px] flex w-[280px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
        style="display: none;">
        <div>
            <span class="block text-theme-sm font-medium text-gray-700 dark:text-gray-300">{{ $authUser?->name }}</span>
            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ $authUser?->email }}</span>
            <div class="mt-2">
                @include('admin.components.badge', [
                    'type' => $roleBadgeType,
                    'text' => $roleLabel,
                ])
            </div>
        </div>

        <ul class="flex flex-col gap-1 border-b border-gray-200 pb-3 pt-4 dark:border-gray-800">
            <li>
                <a href="/admin/profile"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                    {{ __('ui.admin.profile') }}
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}"
                    class="group flex items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                    {{ __('ui.admin.back_to_portal') }}
                </a>
            </li>
        </ul>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit"
                class="group mt-3 flex w-full items-center gap-3 rounded-lg px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                @click="closeDropdown()">
                {{ __('ui.admin.logout') }}
            </button>
        </form>
    </div>
</div>
