<header class="nxl-header">
    <div class="header-wrapper">
        {{-- Header Left --}}
        <div class="header-left d-flex align-items-center gap-4">
            {{-- Mobile Toggler --}}
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>

            {{-- Page Title --}}
            <h5 class="mb-0 d-none d-lg-block">@yield('header', 'Dashboard')</h5>
        </div>

        {{-- Header Right --}}
        <div class="header-right d-flex align-items-center gap-4 ms-auto">

            {{-- Dark/Light Theme Toggle --}}
            <div class="nxl-h-item dark-light-theme">
                <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                    <i class="feather-moon"></i>
                </a>
                <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                    <i class="feather-sun"></i>
                </a>
            </div>

            {{-- Notifications Dropdown --}}
            @include('layouts.partials.notifications-dropdown')

            {{-- User Profile Dropdown --}}
            <div class="dropdown nxl-h-item">
                <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                    @if(auth()->user()->profile?->photo)
                        <img src="{{ Storage::url(auth()->user()->profile->photo) }}" alt="{{ auth()->user()->name }}" class="img-fluid user-avtar me-0">
                    @else
                        <div class="avatar-text avatar-md rounded-circle bg-primary text-white me-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                    <div class="dropdown-header border-0">
                        <div class="d-flex align-items-center">
                            @if(auth()->user()->profile?->photo)
                                <img src="{{ Storage::url(auth()->user()->profile->photo) }}" alt="{{ auth()->user()->name }}" class="img-fluid user-avtar">
                            @else
                                <div class="avatar-text avatar-md rounded-circle bg-primary text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="ms-3">
                                <h6 class="text-dark mb-0">
                                    {{ auth()->user()->name }}
                                    @if(auth()->user()->hasActiveSubscription())
                                        <span class="badge bg-soft-success text-success ms-1">PRO</span>
                                    @endif
                                </h6>
                                <span class="fs-12 fw-medium text-muted">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('profile.index') }}" class="dropdown-item">
                        <i class="feather-user"></i>
                        <span>Profile Details</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="dropdown-item">
                        <i class="feather-settings"></i>
                        <span>Account Settings</span>
                    </a>
                    <div class="dropdown-divider mb-0"></div>
                    <a href="{{ route('logout') }}" class="dropdown-item logout-trigger">
                        <i class="feather-log-out"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
