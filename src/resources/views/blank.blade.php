<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('header')
    @stack('headerstyles')
    <script type="text/javascript" src="{{ asset('/feiron/felaraframe/js/core.js') }}"></script>
    @stack('headerscripts')
</head>

<!-- BEGIN BODY -->
<body>
    @yield('main-content')
    @yield('footer')
    @stack('lastContent')
    @push('footerscripts')
        @stack('OutletResource')
    @endpush
    @stack('footerscripts')
</body>
</html>