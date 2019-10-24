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
                    <h4>@yield('user_name',(auth()->user()->name??''))</h4>
                </div>
            </div>
        </div>
        <ul class="nav nav-sidebar">
            <!-- SIDEBAR MENU -->
            @if(config('felaraframe.appconfig.use_route_as_menu'))
                @foreach (menuGenerator::getMenuFromRoutes() as $Menu)
                    @fesidebarMenu(['href'=>$Menu['href'],'icon'=>($Menu['title']=='home'?'home':'angle-right')])
                        {{$Menu['title']}}
                    @endfesidebarMenu
                @endforeach
            @endif
            @yield('sidebar_menu')
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