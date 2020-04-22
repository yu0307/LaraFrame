@section('topbar')
@php
    $newMails=$newMails??app()->FeFrame->COMs()->getMessage();
@endphp
<!-- BEGIN TOPBAR -->
<div class="topbar">
    <div class="header-left w-50p">
        <div class="topnav w-100p">
            <a class="menutoggle" href="#" data-toggle="sidebar-collapsed"><span class="menu__handle"><span>Menu</span></span></a>
            <ul class="nav nav-icons w-30p">
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
                    <span class="badge badge-danger badge-header">{{$newMails?count($newMails):''}}</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="dropdown-header clearfix">
                        <p class="pull-left">you have {{count($newMails)??0}} New Notifications</p>
                    </li>
                    @foreach ($newMails??[] as $mail)
                        <li class="clearfix">
                            <div class="clearfix bd-green border-bottom p-5">
                                <a href="{{route('LF_Notifications',['MID'=>$mail->id])}}">
                                    <div>
                                        <strong>{{$mail->Sender->name??'System'}}</strong> 
                                        <small class="pull-right text-muted">
                                            <span class="glyphicon glyphicon-time p-r-5"></span>{{$mail->created_at->format('(D)M-d Y')}}
                                        </small>
                                    </div>
                                    {!!Illuminate\Support\Str::limit($mail->subject,150,' ...')!!}
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li class="dropdown" id="user-header">
                <!-- USER DROPDOWN MENU -->
                <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <img src="{{app()->FeFrame->GetProfileImage()}}">
                    
                    <span class="username">Hi, @yield('user_name',(auth()->user()->name??''))</span>
                </a>
                @auth
                <ul class="dropdown-menu">
                    @if (Route::has('Profile'))
                    <li>
                        <a href="{{route('Profile')}}"><i class="icon-user"></i><span>Account Settings</span></a>
                    </li>
                    @endif
                    @if (Route::has('LF_Notifications'))
                    <li>
                        <a href="{{route('LF_Notifications')}}"><i class="fa fa-envelope-o"></i><span>Notifications</span></a>
                    </li>
                    @endif

                    @if (Route::has('LF_controlpanel'))
                    @role('admin')
                        <li>
                            <a href="{{route('LF_controlpanel')}}"><i class="icon-settings"></i><span>Control Panel</span></a>
                        </li>
                    @endrole
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