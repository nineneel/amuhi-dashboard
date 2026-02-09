<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name') }} - Authentication">
    <meta name="keyword" content="amuhi, dashboard, authentication">
    <meta name="author" content="{{ config('app.name') }}">

    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Amuhi Dashboard') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('css/bootstrap.min.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('vendors/css/vendors.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('css/theme.min.css') }}">

    <style>
        #global-loading-overlay {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 30% 30%, rgba(52, 84, 209, 0.12), transparent 35%),
                radial-gradient(circle at 80% 70%, rgba(234, 77, 77, 0.12), transparent 45%),
                rgba(15, 23, 42, 0.45);
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
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')

    <div id="global-loading-overlay" aria-hidden="true">
        <div class="loading-card">
            <div class="spinner-border text-light" role="status" aria-hidden="true"></div>
            <p class="mt-3 mb-0 fw-semibold">Processing…</p>
        </div>
    </div>

    <!-- Vendors JS -->
    <script src="{{ url('vendors/js/vendors.min.js') }}"></script>

    <!-- Apps Init -->
    <script src="{{ url('js/common-init.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
        });
    </script>

    @stack('scripts')
</body>
</html>
