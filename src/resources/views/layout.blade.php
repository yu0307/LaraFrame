@includeIf('felaraframe::header')
@includeIf('felaraframe::footer')
@includeIf('felaraframe::sidebar')
@includeIf('felaraframe::topbar')
<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    @yield('header')
    @stack('headerscripts')
    @stack('headerstyles')
    <meta name="csrf-token" content="{{ csrf_token() }}" >
</head>

<!-- BEGIN BODY -->

<body class="fixed-topbar color-default theme-sdtl bg-light-blue">
    <section>
        @yield('sidebar')
        <!-- BEGIN MAIN CONTENT -->
        <div class="main-content">
            @yield('topbar')
            <div class="page-content">
                <!-- PAGE CONTENT -->
                @yield('content')
            </div>
            @yield('footer')
        </div>
        <!-- END MAIN CONTENT -->
    </section>

    <div class="loader-overlay">
        <!-- PRELOADER -->
    </div>

    <div id="quickview-sidebar" class="sideview">
        <div class="quickview-header">
            @yield('quickview_header')
        </div>
        <div class="quickview">
            <div class="tab-content">
                <!-- SIDEBAR -->
                @yield('sidebar_alt')
            </div>
            <div class="quickview_footer">
                @yield('sidebar_alt_footer')
            </div>
        </div>
    </div>

    @stack('footerscripts')
    @stack('footerstyles')
</body>

</html>