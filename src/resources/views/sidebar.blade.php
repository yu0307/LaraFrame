@section('sidebar')
<!-- BEGIN SIDEBAR -->
<div class="sidebar">
    <div class="logopanel">
        <!-- LOGO -->
        <a class="logoText" href="{{Route::has('home')?route('home'):'/'}}">{{config('app.name')}}</a>
    </div>
    <div class="sidebar-inner">
        <div class="sidebar-top">
            <!-- TOP ELEMENT: SEARCH, IMAGE -->
            <div class="userlogged clearfix">
                <i class="icon icons-faces-users-01"></i>
                <div class="user-details">
                    <h4>@yield('user_name','Unknown')</h4>
                </div>
            </div>
        </div>
        <ul class="nav nav-sidebar">
            <!-- SIDEBAR MENU -->
            @yield('sidebar_menu')
            <li class="tm nav-active active"><a href="my-link.html"><i class="icon-home"></i><span>Test Active Menu 1</span></a></li>
            <li class="tm nav-parent">
                <a href="#"><i class="icon-puzzle"></i><span>test Menu 2</span> <span class="fa arrow"></span></a>
                <ul class="children collapse">
                    <li><a href="submenu1.html">Submenu 1</a></li>
                    <li><a href="submenu2.html">Submenu 2</a></li>
                    <li><a href="submenu3.html">Submenu 3</a></li>
                </ul>
            </li>
        </ul>
        <div class="sidebar-widgets">
            <!-- SIDEBAR WIDGET -->
            @yield('sidebar_widget')
        </div>
        <div class="sidebar-footer clearfix">
            <!-- QUICK ACCESS -->
            @yield('sidebar_footer')
        </div>
    </div>
</div>
<!-- END SIDEBAR -->
@endsection