@php
    $authUser = auth()->user();
    $profilePhoto = $authUser?->profile?->photo ? asset('storage/' . $authUser->profile->photo) : null;
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
        <span class="mr-3 flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
            @if ($profilePhoto)
                <img src="{{ $profilePhoto }}" alt="User" class="h-full w-full object-cover" />
            @else
                {{ strtoupper(substr($authUser?->name ?? 'U', 0, 1)) }}
            @endif
        </span>

        <span class="block mr-1 font-medium text-theme-sm">{{ $authUser?->name }}</span>

        <svg class="w-5 h-5 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
        style="display: none;">
        <div>
            <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-300">{{ $authUser?->name }}</span>
            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ $authUser?->email }}</span>
        </div>

		        <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800">
		            <li>
		                <a href="{{ route('profile.index') }}"
		                    class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
		                    {{ __('ui.nav.profile') }}
		                </a>
		            </li>
		            <li>
		                <a href="{{ route('settings.index') }}"
		                    class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
		                    {{ __('ui.nav.app_settings') }}
		                </a>
		            </li>
                    @if ($authUser?->isAdmin())
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                {{ __('ui.nav.go_to_admin') }}
                            </a>
                        </li>
                    @endif
		        </ul>

	        <form method="POST" action="{{ route('logout') }}">
	            @csrf
	            <button type="submit"
	                class="flex items-center w-full gap-3 px-3 py-2 mt-3 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
	                @click="closeDropdown()">
	                {{ __('ui.buttons.sign_out') }}
	            </button>
	        </form>
	    </div>
	</div>
