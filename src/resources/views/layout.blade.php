<!DOCTYPE html>
<html lang="en">
@includeFirst([$theme.'::header', 'felaraframe::header'])

<head>
    <title>@yield('title')</title>
    @yield('header')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- BEGIN BODY -->
@includeFirst([$theme.'::index', 'felaraframe::index'])   

</html>