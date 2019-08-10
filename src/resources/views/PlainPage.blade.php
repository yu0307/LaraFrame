@includeIf('felaraframe::header')
@includeIf('felaraframe::footer')
<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    @yield('header')
    @stack('headerscripts')
    @stack('headerstyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- BEGIN BODY -->

<body class="color-default bg-light-blue">
    <section>
        <!-- BEGIN MAIN CONTENT -->
        <div class="main-content m-l-0">
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
    @stack('footerscripts')
    @stack('footerstyles')
</body>

</html>