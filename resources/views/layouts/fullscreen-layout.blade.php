<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

			    <title>{{ config('app.name') }} | {{ $title ?? __('ui.meta.dashboard') }}</title>

	    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
	    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}">

	    <!-- Scripts -->
	    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data>

    @yield('content')

</body>

@stack('scripts')

</html>
