@includeIf('LaraFrame::header')
@includeIf('LaraFrame::footer')
@includeIf('LaraFrame::sidebar')
@includeIf('LaraFrame::topbar')
<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    @yield('header')
    @stack('headerscripts')
    @stack('headerstyles')
</head>

<!-- BEGIN BODY -->

<body class="fixed-topbar color-default theme-sdtl bg-light-blue">
    <section>
        @yield('sidebar')
        <!-- BEGIN MAIN CONTENT -->
        <div class="main-content">
            @yield('topbar')
            <div class="page-content p-b-30">
                <!-- PAGE CONTENT -->
                @yield('content')
                @yield('footer')
            </div>
        </div>
        <!-- END MAIN CONTENT -->
    </section>

    <div class="loader-overlay">
        <!-- PRELOADER -->
    </div>

    <div class="sideview">
        <!-- SIDEBAR -->
        @yield('sidebar_alt')
    </div>

    @stack('footerscripts')
    @stack('footerstyles')
</body>

</html>