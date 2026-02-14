@php
    $authUser = auth()->user();
    $notifications = $authUser
        ? $authUser->notifications()->latest()->limit(8)->get()
        : collect();
    $unreadCount = $authUser
        ? $authUser->notifications()->whereNull('read_at')->count()
        : 0;
@endphp

<div class="relative" x-data="{
    dropdownOpen: false,
    notifying: {{ $unreadCount > 0 ? 'true' : 'false' }},
    toggleDropdown() {
        this.dropdownOpen = !this.dropdownOpen;
        this.notifying = false;
    },
    closeDropdown() {
        this.dropdownOpen = false;
    }
}" @click.away="closeDropdown()">
    <button
        class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
        @click="toggleDropdown()" type="button">
        <span x-show="notifying" class="absolute right-0 top-0.5 z-1 h-2 w-2 rounded-full bg-orange-400">
            <span class="absolute inline-flex w-full h-full bg-orange-400 rounded-full opacity-75 -z-1 animate-ping"></span>
        </span>

        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M10.75 2.29248C10.75 1.87827 10.4143 1.54248 10 1.54248C9.58583 1.54248 9.25004 1.87827 9.25004 2.29248V2.83613C6.08266 3.20733 3.62504 5.9004 3.62504 9.16748V14.4591H3.33337C2.91916 14.4591 2.58337 14.7949 2.58337 15.2091C2.58337 15.6234 2.91916 15.9591 3.33337 15.9591H4.37504H15.625H16.6667C17.0809 15.9591 17.4167 15.6234 17.4167 15.2091C17.4167 14.7949 17.0809 14.4591 16.6667 14.4591H16.375V9.16748C16.375 5.9004 13.9174 3.20733 10.75 2.83613V2.29248ZM14.875 14.4591V9.16748C14.875 6.47509 12.6924 4.29248 10 4.29248C7.30765 4.29248 5.12504 6.47509 5.12504 9.16748V14.4591H14.875ZM8.00004 17.7085C8.00004 18.1228 8.33583 18.4585 8.75004 18.4585H11.25C11.6643 18.4585 12 18.1228 12 17.7085C12 17.2943 11.6643 16.9585 11.25 16.9585H8.75004C8.33583 16.9585 8.00004 17.2943 8.00004 17.7085Z"
                fill="" />
        </svg>
    </button>

	    <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark sm:w-[361px] lg:right-0"
	        style="display: none;">
	        <div class="mb-3 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
	            <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('ui.header.notifications') }}</h5>
	            @if ($unreadCount > 0)
	                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
	                    {{ __('ui.header.unread_count', ['count' => $unreadCount]) }}
	                </span>
	            @endif
	        </div>

        <ul class="custom-scrollbar flex h-auto flex-col overflow-y-auto">
            @forelse ($notifications as $notification)
                <li>
                    <div class="rounded-lg border-b border-gray-100 px-4 py-3 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $notification->title }}</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $notification->message }}</p>
                        <div class="mt-1 flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                            <span>{{ ucfirst($notification->type) }}</span>
                            <span class="h-1 w-1 rounded-full bg-gray-400"></span>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </li>
	            @empty
	                <li>
	                    <div class="rounded-lg px-4 py-5 text-center text-sm text-gray-500 dark:text-gray-400">
	                        {{ __('ui.header.no_notifications') }}
	                    </div>
	                </li>
	            @endforelse
	        </ul>

	        <a href="{{ route('notifications.index') }}"
	            class="mt-3 inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
	            {{ __('ui.header.view_all_notifications') }}
	        </a>
	    </div>
	</div>
