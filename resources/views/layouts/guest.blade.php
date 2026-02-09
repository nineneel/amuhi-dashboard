<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Amuhi Dashboard') }}</title>

    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">
    <!-- Scripts -->
    @vite(['resources/css/guest.css', 'resources/js/guest.js'])
</head>
<body class="auth-body">
    <script src="{{ asset('vendors/js/vendors.min.js') }}" defer></script>
    @yield('content')
</body>
</html>
