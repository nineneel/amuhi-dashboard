<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @yield('header', 'Dashboard')
        </h2>

        <div class="flex items-center space-x-4">
            @include('layouts.partials.notifications-dropdown')

            <!-- Profile Dropdown -->
            <div class="relative">
                <button class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                    <span>{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>
