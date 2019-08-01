@section('topbar')
<!-- BEGIN TOPBAR -->
<div class="topbar">
    <div class="header-left">
        <div class="topnav">
            <a class="menutoggle" href="#" data-toggle="sidebar-collapsed"><span class="menu__handle"><span>Menu</span></span></a>
            <ul class="nav nav-icons">
                @yield('topbar_menus')
                <!-- testing remove on production -->
                <!-- <li><span class="octicon octicon-cloud-upload"></span></li> -->
            </ul>
            <!-- LEFT TOPBAR: MENU OR ICONS -->
        </div>
    </div>
    <div class="header-right">
        <ul class="header-menu nav navbar-nav">
            <li class="dropdown" id="notifications-header">
                <!-- NOTIFICATION DROPDOWN MENU -->
                <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <i class="icon-bell"></i>
                    <span class="badge badge-danger badge-header">{{$notificationCount??''}}</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-header clearfix">
                        <p class="pull-left">you have {{$notificationCount??0}} Notifications</p>
                    </li>
                </ul>
            </li>
            <li class="dropdown" id="user-header">
                <!-- USER DROPDOWN MENU -->
                <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <img src="{{$user_profile_pic??asset('FeIron/LaraFrame/images/avatars/avatar7.png')}}" alt="user image">
                    <span class="username">Hi {{$user_name??''}}</span>
                </a>
                @auth
                <ul class="dropdown-menu">
                    @if (Route::has('Profile'))
                    <li>
                        <a href="{{route('Profile')}}"><i class="icon-user"></i><span>My Profile</span></a>
                    </li>
                    @endif
                    @if (Route::has('user_setting'))
                    <li>
                        <a href="{{route('user_setting')}}"><i class="icon-settings"></i><span>Account Settings</span></a>
                    </li>
                    @endif
                    @if (Route::has('logout') || Route::has('Fe_Logout'))
                    <li>
                        <a href="{{route('Fe_Logout')??route('logout')}}"><i class="icon-logout"></i><span>Logout</span></a>
                    </li>
                    @endif
                </ul>
                @endauth
            </li>
        </ul>
    </div>
    <!-- END HEADER RIGHT -->
</div>
<!-- END TOPBAR -->
@endsection