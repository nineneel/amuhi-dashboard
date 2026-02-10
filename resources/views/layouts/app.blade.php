@php
    $themePreference = auth()->user()?->settings?->theme ?? 'system';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ $themePreference === 'dark' ? 'app-skin-dark' : '' }}" data-theme="{{ $themePreference }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="AMUHI - Asosiasi Milenial Umroh Haji Indonesia. Member dashboard for programs, events, and membership management.">
    <meta name="keyword" content="amuhi, umroh, haji, dashboard, membership">
    <meta name="author" content="AMUHI">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'AMUHI') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap.min.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('vendors/css/vendors.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('css/theme.min.css') }}">

    <style>
        /* Global loading overlay */
        #global-loading-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 30% 30%, rgba(52, 84, 209, 0.12), transparent 35%),
                radial-gradient(circle at 80% 70%, rgba(234, 77, 77, 0.12), transparent 45%),
                rgba(15, 23, 42, 0.45);
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 150ms ease, visibility 150ms ease;
            z-index: 12000;
        }

        #global-loading-overlay.show {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        .loading-card {
            min-width: 220px;
            padding: 22px 26px;
            border-radius: 14px;
            background: #0f172a;
            color: #e2e8f0;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
            text-align: center;
        }

        .loading-card .spinner-border {
            width: 2.5rem;
            height: 2.5rem;
            border-width: 0.25rem;
        }

        /* Remove heavy blur on modal backdrops */
        .modal-backdrop {
            background-color: rgba(17, 24, 39, 0.45) !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        body.modal-open .nxl-container,
        body.modal-open .main-content {
            filter: none !important;
            -webkit-filter: none !important;
        }

        /* Header dropdowns open on click only */
        .nxl-h-item .nxl-h-dropdown {
            display: none;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .nxl-h-item .nxl-h-dropdown.show {
            display: block;
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .nxl-h-item:hover > .nxl-h-dropdown:not(.show) {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }

        /* Avatar images maintain cover fit without stretching */
        .avatar-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-avtar {
            width: 46px;
            height: 46px;
            min-width: 46px;
            min-height: 46px;
            max-width: 46px;
            max-height: 46px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Settings drawer on mobile */
        @media (max-width: 991.98px) {
            .content-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                max-height: 100vh;
                width: 0;
                flex: 0 0 0;
                max-width: 82vw;
                transform: translateX(-110%);
                transition: transform 0.25s ease, box-shadow 0.25s ease, width 0.2s ease;
                z-index: 11000;
                box-shadow: 0 0 0 rgba(0, 0, 0, 0);
            }

            .content-sidebar.app-sidebar-open {
                width: 280px;
                flex: 0 0 280px;
                transform: translateX(0);
                box-shadow: 0 20px 50px rgba(15, 23, 42, 0.35);
            }

            .content-area {
                width: 100%;
                flex: 1 1 100%;
            }

            .content-sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.35);
                z-index: 10999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease, visibility 0.2s ease;
            }

            .content-sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }

            body.sidebar-drawer-open {
                overflow: hidden;
            }
        }

        /* Keep sidebar expanded on desktop even if minimenu class is present */
        @media (min-width: 992px) {
            .minimenu .nxl-navigation {
                left: 0 !important;
                width: 280px !important;
            }
            .minimenu .nxl-container {
                margin-left: 280px !important;
            }
            .minimenu .page-header {
                left: 280px !important;
            }
            /* Always keep desktop navigation expanded */
            .nxl-navigation {
                left: 0 !important;
                width: 280px !important;
            }
            .nxl-container {
                margin-left: 280px !important;
            }
        .page-header {
            left: 280px !important;
        }
    }

        .dashboard-next-event-card {
            background-color: #f1f3f9;
            border-color: #e5e7eb;
        }

        html.app-skin-dark .dashboard-next-event-card {
            background-color: #121a2d;
            border-color: #1b2436;
        }

        .notification-item--read {
            background-color: #f5f7fb;
            border-color: #e2e8f0;
        }

        html.app-skin-dark .notification-item--read {
            background-color: #121a2d;
            border-color: #1b2436;
        }

        /* Compact header */
        .nxl-header.nxl-header-compact {
            height: 50px;
            min-height: 50px;
        }

        .nxl-header.nxl-header-compact .header-wrapper {
            height: 50px;
            min-height: 50px;
            padding: 0 20px;
        }

        /* Smaller user avatar in header */
        .user-avtar-sm {
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
            max-width: 32px;
            max-height: 32px;
            border-radius: 50%;
            object-fit: cover;
            aspect-ratio: 1 / 1;
            display: block;
        }

        .avatar-sm {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            font-size: 14px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        /* Profile dropdown trigger */
        .profile-dropdown-trigger {
            padding: 6px 12px;
            border-radius: 8px;
            transition: background-color 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease;
            text-decoration: none;
            color: #111827;
            cursor: pointer;
        }

        .profile-dropdown-trigger:hover {
            background-color: rgba(0, 0, 0, 0.05);
            box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        html.app-skin-dark .profile-dropdown-trigger:hover {
            background-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.2);
        }

        html.app-skin-dark .profile-dropdown-trigger {
            color: #e5e7eb;
        }

        .profile-dropdown-trigger:active {
            transform: translateY(1px);
        }

        .profile-dropdown-trigger .user-avtar-sm,
        .profile-dropdown-trigger .avatar-text {
            width: 32px;
            height: 32px;
            min-width: 32px;
            min-height: 32px;
        }

        .theme-toggle-item {
            cursor: pointer;
        }

        /* Breadcrumb styling */
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb .breadcrumb-item a {
            color: #4b5563;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .breadcrumb .breadcrumb-item a:hover {
            color: var(--bs-primary);
        }

        .breadcrumb .breadcrumb-item.active,
        .breadcrumb .breadcrumb-item:last-child {
            color: #1f2937;
        }

        html.app-skin-dark .breadcrumb .breadcrumb-item a {
            color: #cbd5e1;
        }

        html.app-skin-dark .breadcrumb .breadcrumb-item a:hover {
            color: var(--bs-primary);
        }

        html.app-skin-dark .breadcrumb .breadcrumb-item.active,
        html.app-skin-dark .breadcrumb .breadcrumb-item:last-child {
            color: #e5e7eb;
        }

        /* Adjust content area for compact header */
        .nxl-container {
            top: 50px !important;
            min-height: calc(100vh - 30px) !important;
            padding-top: 32px !important;
        }

        .nxl-content {
            padding-top: 0 !important;
        }

        .main-content {
            padding-top: 0 !important;
        }
    </style>

    @stack('styles')
</head>
<body data-theme-preference="{{ $themePreference }}" data-theme-update-url="{{ route('settings.appearance') }}">
    @include('layouts.partials.sidebar')

    @include('layouts.partials.header')

    <main class="nxl-container">
        <div class="nxl-content d-flex flex-column min-vh-100">
            <x-payment-banner :user="auth()->user()" />

            <div class="main-content flex-grow-1 @yield('content-class')">
                @yield('content')
            </div>

            @include('layouts.partials.footer', [
                'footerClass' => trim($__env->yieldContent('footer-class', 'mt-4 pt-4')),
            ])
        </div>
    </main>

    <div id="global-loading-overlay" aria-hidden="true">
        <div class="loading-card">
            <div class="spinner-border text-light" role="status" aria-hidden="true"></div>
            <p class="mt-3 mb-0 fw-semibold">Processing…</p>
        </div>
    </div>

    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="logoutConfirmLabel">Ready to leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">You are about to log out of your account.</p>
                    <p class="text-muted mb-0">Any unsaved changes will be lost.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Stay logged in</button>
                    <button type="button" class="btn btn-danger" data-confirm-logout>
                        <i class="feather-log-out me-2"></i>Log out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors JS -->
    <script src="{{ url('vendors/js/vendors.min.js') }}"></script>

    <!-- Apps Init -->
    <script src="{{ url('js/common-init.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const html = document.documentElement;
            let themePreference = document.body?.dataset.themePreference || 'system';
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const themeToggleSwitch = document.getElementById('theme-toggle-switch');
            const themeToggleItem = document.querySelector('.theme-toggle-item');

            const resolveTheme = () => {
                if (themePreference === 'system') {
                    return mediaQuery.matches ? 'dark' : 'light';
                }

                return themePreference;
            };

            const applyTheme = (mode, persist = true) => {
                html.classList.toggle('app-skin-dark', mode === 'dark');
                if (persist) {
                    localStorage.setItem('app-skin-dark', mode === 'dark' ? 'app-skin-dark' : 'app-skin-light');
                }
            };

            const syncThemeSwitch = mode => {
                if (!themeToggleSwitch) {
                    return;
                }

                themeToggleSwitch.checked = mode === 'dark';
            };

            const updateThemePreference = (mode) => {
                const updateUrl = document.body?.dataset.themeUpdateUrl;
                const token = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content');

                themePreference = mode;
                document.body.dataset.themePreference = mode;

                if (!updateUrl || !token) {
                    return;
                }

                fetch(updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ theme: mode }),
                }).catch(() => {});
            };

            const initialMode = resolveTheme();
            applyTheme(initialMode, false);
            syncThemeSwitch(initialMode);

            if (themePreference === 'system') {
                mediaQuery.addEventListener('change', () => {
                    const nextMode = resolveTheme();
                    applyTheme(nextMode, false);
                    syncThemeSwitch(nextMode);
                });
            }

            const toggleTheme = mode => {
                applyTheme(mode);
                updateThemePreference(mode);
                syncThemeSwitch(mode);
            };

            themeToggleSwitch?.addEventListener('click', event => {
                event.stopPropagation();
            });

            themeToggleSwitch?.addEventListener('change', event => {
                const mode = event.target.checked ? 'dark' : 'light';
                toggleTheme(mode);
            });

            themeToggleItem?.addEventListener('click', event => {
                event.stopPropagation();
                const nextMode = themeToggleSwitch?.checked ? 'light' : 'dark';
                if (themeToggleSwitch) {
                    themeToggleSwitch.checked = nextMode === 'dark';
                }
                toggleTheme(nextMode);
            });

            const forceExpandedNav = () => {
                document.documentElement.classList.remove('minimenu');
                document.body.classList.remove('minimenu');
                localStorage.setItem('nexel-classic-dashboard-menu-mini-theme', 'menu-expend-theme');

                const logoFull = document.querySelector('.logo-full');
                const logoAbbr = document.querySelector('.logo-abbr');
                if (logoFull) logoFull.style.display = 'block';
                if (logoAbbr) logoAbbr.style.display = 'none';
            };

            // run once and on resize to override theme auto-collapse
            forceExpandedNav();
            window.addEventListener('resize', forceExpandedNav);

            const closeAll = () => document.querySelectorAll('.nxl-h-dropdown.show')
                .forEach(menu => menu.classList.remove('show'));

            document.addEventListener('click', closeAll);

            document.querySelectorAll('.nxl-h-item').forEach(item => {
                const menu = item.querySelector('.nxl-h-dropdown');
                const trigger = item.querySelector('a[data-bs-toggle="dropdown"], a.nxl-head-link');
                if (!menu || !trigger) {
                    return;
                }
                trigger.addEventListener('click', event => {
                    event.preventDefault();
                    event.stopPropagation();
                    const isOpen = menu.classList.contains('show');
                    closeAll();
                    if (!isOpen) {
                        menu.classList.add('show');
                    }
                });
            });

            const loader = document.getElementById('global-loading-overlay');
            let pendingRequests = 0;

            const showLoader = () => {
                pendingRequests += 1;
                loader?.classList.add('show');
            };

            const hideLoader = () => {
                pendingRequests = Math.max(0, pendingRequests - 1);
                if (pendingRequests === 0) {
                    loader?.classList.remove('show');
                }
            };

            window.showAppLoader = showLoader;
            window.hideAppLoader = hideLoader;

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', () => {
                    if (form.hasAttribute('data-skip-loader')) {
                        return;
                    }

                    showLoader();
                });
            });

            if (window.fetch) {
                const originalFetch = window.fetch.bind(window);
                window.fetch = (...args) => {
                    showLoader();
                    return originalFetch(...args).finally(hideLoader);
                };
            }

            if (window.XMLHttpRequest) {
                const originalOpen = XMLHttpRequest.prototype.open;
                XMLHttpRequest.prototype.open = function (...args) {
                    this.addEventListener('loadstart', showLoader);
                    this.addEventListener('loadend', hideLoader);
                    return originalOpen.apply(this, args);
                };
            }

            if (window.axios?.interceptors) {
                window.axios.interceptors.request.use(config => {
                    showLoader();
                    return config;
                }, error => {
                    hideLoader();
                    return Promise.reject(error);
                });

                window.axios.interceptors.response.use(response => {
                    hideLoader();
                    return response;
                }, error => {
                    hideLoader();
                    return Promise.reject(error);
                });
            }

            const logoutForm = document.getElementById('logout-form');
            const logoutModalEl = document.getElementById('logoutConfirmModal');
            const logoutConfirmButton = logoutModalEl?.querySelector('[data-confirm-logout]');
            const logoutModal = logoutModalEl ? bootstrap.Modal.getOrCreateInstance(logoutModalEl) : null;

            const handleLogout = () => {
                if (!logoutForm || !logoutModal) {
                    return;
                }

                logoutModal.show();
            };

            document.querySelectorAll('.logout-trigger').forEach(trigger => {
                trigger.addEventListener('click', event => {
                    event.preventDefault();
                    handleLogout();
                });
            });

            logoutConfirmButton?.addEventListener('click', () => {
                if (!logoutForm) {
                    return;
                }

                showLoader();
                logoutForm.submit();
            });

            const sidebar = document.querySelector('.content-sidebar');
            const sidebarBackdrop = document.querySelector('.content-sidebar-backdrop');
            const openSidebar = () => {
                if (!sidebar) {
                    return;
                }
                sidebar.classList.add('app-sidebar-open');
                sidebarBackdrop?.classList.add('show');
                document.body.classList.add('sidebar-drawer-open');
            };

            const closeSidebar = () => {
                sidebar?.classList.remove('app-sidebar-open');
                sidebarBackdrop?.classList.remove('show');
                document.body.classList.remove('sidebar-drawer-open');
            };

            document.querySelectorAll('.app-sidebar-open-trigger').forEach(trigger => {
                trigger.addEventListener('click', event => {
                    event.preventDefault();
                    openSidebar();
                });
            });

            document.querySelectorAll('.app-sidebar-close-trigger').forEach(trigger => {
                trigger.addEventListener('click', event => {
                    event.preventDefault();
                    closeSidebar();
                });
            });

            sidebarBackdrop?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
