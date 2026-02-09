<aside class="w-64 bg-white shadow-md min-h-screen">
    <nav class="mt-5 px-3">
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
            <span>Dashboard</span>
        </a>

        <div class="border-t my-3"></div>

        <a href="{{ route('events.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
            <span>Events</span>
        </a>

        <a href="{{ route('programs.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
            <span>Programs</span>
        </a>

        <div class="border-t my-3"></div>

        <a href="{{ route('invoices.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
            <span>Invoices</span>
        </a>

        <div class="border-t my-3"></div>

        <a href="{{ route('profile.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
            <span>Profile</span>
        </a>

        <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md">
            <span>Settings</span>
        </a>

        <div class="border-t my-3"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-md text-left">
                <span>Logout</span>
            </button>
        </form>
    </nav>
</aside>
