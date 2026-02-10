<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand">
                <img src="{{ asset('images/logo-full.png') }}" alt="{{ config('app.name') }}" class="logo logo-lg" style="max-height: 200px; width: auto;">
                <img src="{{ asset('images/logo-abbr.png') }}" alt="{{ config('app.name') }}" class="logo logo-sm" style="max-height: 360px; width: auto;">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Navigation</label>
                </li>

                {{-- Dashboard --}}
                <li class="nxl-item">
                    <a href="{{ route('dashboard') }}" class="nxl-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-home"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>

                {{-- Events (Paid) --}}
                <li class="nxl-item">
                    <a href="{{ route('events.index') }}" class="nxl-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-calendar"></i></span>
                        <span class="nxl-mtext">Events</span>
                        @unless(auth()->user()->hasActiveSubscription())
                            <span class="badge bg-soft-warning text-warning ms-auto">Paid</span>
                        @endunless
                    </a>
                </li>

                {{-- Programs (Paid) --}}
                <li class="nxl-item">
                    <a href="{{ route('programs.index') }}" class="nxl-link {{ request()->routeIs('programs.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-grid"></i></span>
                        <span class="nxl-mtext">Programs</span>
                        @unless(auth()->user()->hasActiveSubscription())
                            <span class="badge bg-soft-warning text-warning ms-auto">Paid</span>
                        @endunless
                    </a>
                </li>

                <li class="nxl-item nxl-caption">
                    <label>Account</label>
                </li>

                {{-- Profile --}}
                <li class="nxl-item">
                    <a href="{{ route('profile.index') }}" class="nxl-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-user"></i></span>
                        <span class="nxl-mtext">Profile</span>
                    </a>
                </li>

                {{-- Settings --}}
                <li class="nxl-item">
                    <a href="{{ route('settings.index') }}" class="nxl-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-settings"></i></span>
                        <span class="nxl-mtext">Settings</span>
                    </a>
                </li>

                {{-- Invoices (Account) --}}
                <li class="nxl-item">
                    <a href="{{ route('invoices.index') }}" class="nxl-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <span class="nxl-micon"><i class="feather-file-text"></i></span>
                        <span class="nxl-mtext">Invoices</span>
                        @unless(auth()->user()->hasActiveSubscription())
                            <span class="badge bg-soft-warning text-warning ms-auto">Paid</span>
                        @endunless
                    </a>
                </li>

                {{-- Logout --}}
                <li class="nxl-item">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}" class="nxl-link logout-trigger">
                        <span class="nxl-micon"><i class="feather-log-out"></i></span>
                        <span class="nxl-mtext">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
