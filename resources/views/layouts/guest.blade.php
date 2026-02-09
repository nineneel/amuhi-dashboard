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

    @stack('styles')
</head>
<body>
    @yield('content')

    <!-- Vendors JS -->
    <script src="{{ url('vendors/js/vendors.min.js') }}"></script>

    <!-- Apps Init -->
    <script src="{{ url('js/common-init.min.js') }}"></script>

    @stack('scripts')
</body>
</html>
