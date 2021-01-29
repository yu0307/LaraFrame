<!DOCTYPE html>
<html lang="en">
@php
    foreach(app()->FeFrame->getInitBlocks()??[] as $initBlock){
        $initBlock->execute(request());
    }
@endphp
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    @yield('header')
    @stack('headerstyles')
    @stack('headerscripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- BEGIN BODY -->
<body>
    @include($siteInfo['theme'].'::index')
    @stack('footerscripts')
</body>
</html>