@section('sidebar')
<!-- BEGIN SIDEBAR -->
<div class="sidebar">
    <div class="logopanel">
        <!-- LOGO -->
        <a class="logoText" href="{{Route::has('home')?route('home'):'/'}}">{{config('app.name')}}</a>
    </div>
    <div class="sidebar-inner">
        @switch(($siteInfo['themeSettings']['sb_topdisplay']??'None'))
            @case('Profile Image')
                <div class="sidebar-top big-img" style="display: block;">
                    <div class="user-image">
                        <img src="{{app()->FeFrame->GetProfileImage(300)}}" class="img-responsive img-circle">
                    </div>
                    <h4>@yield('user_name',(auth()->user()->name??''))</h4>
                </div>
                @break
            @case('Mini Profile Image')
                <div class="sidebar-top small-img clearfix" style="display: block;">
                    <div class="user-image">
                        <img src="{{app()->FeFrame->GetProfileImage()}}" class="img-responsive img-circle">
                    </div>
                    <div class="user-details">
                        <h4>@yield('user_name',(auth()->user()->name??''))</h4>
                    </div>
                </div>
                @break
            @case('Icon')
                <div class="sidebar-top">
                    <!-- TOP ELEMENT: SEARCH, IMAGE -->
                    <div class="userlogged clearfix">
                        <i class="icon icons-faces-users-01"></i>
                        <div class="user-details">
                            <h4>@yield('user_name',(auth()->user()->name??''))</h4>
                        </div>
                    </div>
                </div>
                @break
            @default
                <div class="sidebar-top" style="display: none;"></div>
        @endswitch
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