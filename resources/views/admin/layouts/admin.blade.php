<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | {{ $title ?? 'Admin' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('sidebar', {
                isExpanded: false,
                isMobileOpen: false,
                isHovered: false,

                init() {
                    const savedState = localStorage.getItem('adminSidebarExpanded');

                    if (window.innerWidth >= 1280) {
                        this.isExpanded = savedState === null ? true : savedState === 'true';
                    } else {
                        this.isExpanded = false;
                    }

                    this.isMobileOpen = false;

                    window.addEventListener('resize', () => {
                        this.handleResize();
                    });
                },

                handleResize() {
                    if (window.innerWidth < 1280) {
                        if (this.isMobileOpen) {
                            this.isMobileOpen = false;
                        }
                    } else {
                        this.isMobileOpen = false;

                        const savedState = localStorage.getItem('adminSidebarExpanded');
                        this.isExpanded = savedState === null ? true : savedState === 'true';
                    }
                },

                toggleExpanded() {
                    this.isExpanded = !this.isExpanded;
                    this.isMobileOpen = false;

                    if (window.innerWidth >= 1280) {
                        localStorage.setItem('adminSidebarExpanded', this.isExpanded);
                    }
                },

                toggleMobileOpen() {
                    this.isMobileOpen = !this.isMobileOpen;
                },

                setMobileOpen(value) {
                    this.isMobileOpen = value;
                },

                setHovered(value) {
                    if (window.innerWidth >= 1280 && !this.isExpanded) {
                        this.isHovered = value;
                    }
                },
            });
        });
    </script>
</head>

<body>
    <div class="min-h-screen xl:flex sidebar-expanded" x-data
        :class="{ 'sidebar-expanded': $store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen }">
        @include('layouts.backdrop')
        @include('admin.components.sidebar')

        <div
            class="ml-0 flex-1 transition-all duration-300 ease-in-out xl:ml-[90px] [.sidebar-expanded_&]:xl:ml-[290px]">
            @include('admin.components.header')

            <div class="mx-auto max-w-(--breakpoint-2xl) p-4 md:p-6">
                @yield('content')
            </div>
        </div>
    </div>
</body>

@stack('scripts')

</html>
