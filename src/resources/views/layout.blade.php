<!DOCTYPE html>
<html lang="en">
@includeFirst([$theme.'::header', 'felaraframe::header'])
@includeFirst([$theme.'::footer', 'felaraframe::footer'])

<head>
    <title>@yield('title')</title>
    @yield('header')
    @stack('headerstyles')
    @stack('headerscripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- BEGIN BODY -->
@includeFirst([$theme.'::index', 'felaraframe::index'])   

</html>