<!DOCTYPE html>
<html lang="en">
@php
    foreach(app()->FeFrame->getInitBlocks()??[] as $initBlock){
        $initBlock->execute(request());
    }
@endphp

@section('main_menu')
    @foreach (app()->FeFrame->menuGenerator()->getMenu()??[] as $Menu)
        <li class="tm">
            <a href="{{$Menu['href']??'#'}}"><i class="{{$Menu['icon']??'fa fa-angle-right'}}"></i><span>{!!$Menu['title']!!}</span></a>
        </li>
    @endforeach
    @if(config('felaraframe.appconfig.use_route_as_menu'))
        @foreach (menuGenerator::getMenuFromRoutes() as $Menu)
            <x-fe-sidebar-menu
                :menu="$Menu"
            >
                {{$Menu['title']}}
            </x-fe-sidebar-menu>
        @endforeach
    @endif
@endsection

@if(($siteInfo['theme']!='felaraframe') && isset($siteInfo['Setting']['tm_force_bootstrap']))    
    @php
        $siteInfo['Setting']['tm_force_bootstrap']=(is_array($siteInfo['Setting']['tm_force_bootstrap'])===false?[$siteInfo['Setting']['tm_force_bootstrap']]:$siteInfo['Setting']['tm_force_bootstrap']);
    @endphp
    @if (in_array('Jquery',$siteInfo['Setting']['tm_force_bootstrap']))
        @php
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-3.1.0.min.js'),'footerscripts',true);
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-migrate-3.0.0.min.js'),'footerscripts',true);
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js'),'footerscripts',true);
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/global.js'),'footerscripts',true);
        @endphp
    @endif

    @if (in_array('JqueryUI',$siteInfo['Setting']['tm_force_bootstrap']))
        @php
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.css'),'headerstyles',true);
        @endphp
        @php
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js'),'footerscripts',true);
        @endphp
    @endif
    
    @if (in_array('Bootstrap',$siteInfo['Setting']['tm_force_bootstrap']))
        @php
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap/css/bootstrap.min.css'),'headerstyles',true);
        @endphp
        @php
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap/js/bootstrap.min.js'),'footerscripts',true);
        @endphp
    @endif
    
    @if (in_array('fontAwesome',$siteInfo['Setting']['tm_force_bootstrap']))
        @php
            app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/icons/font-awesome/font-awesome.min.css'),'headerstyles',true);
        @endphp
    @endif
@endif

@includeIf($siteInfo['theme'].'::header')
@includeIf($siteInfo['theme'].'::footer')
@includeIf($siteInfo['theme'].'::sidebar')
@php
    $resoucesList=[];
@endphp
@foreach (app()->FeFrame->getResources() as $Location=>$Resources)
        @if ($Location=='push')
            @foreach ($Resources as $section=>$assets)
                @push($section)
                    @foreach ($assets as $key=>$asset)
                        @if (false=== in_array($key,$resoucesList))
                            @php
                                array_push($resoucesList,$key)
                            @endphp
                            {!!$asset!!}
                        @endif
                    @endforeach
                @endpush
            @endforeach
        @else
            @foreach ($Resources as $section=>$assets)
                @prepend($section)
                    @foreach ($assets as $key=>$asset)
                        @if (false=== in_array($key,$resoucesList))
                            @php
                                array_push($resoucesList,$key)
                            @endphp
                            {!!$asset!!}
                        @endif
                    @endforeach
                @endprepend
            @endforeach
        @endif
@endforeach

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    @yield('header')
    @stack('headerstyles')
    @stack('headerscripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<!-- BEGIN BODY -->
@if($siteInfo['theme']!='felaraframe')
<body>
@endif
@include($siteInfo['theme'].'::index')   

@if($siteInfo['theme']!='felaraframe')
    @push('footerscripts')
        @stack('OutletResource')
    @endpush
    @stack('footerscripts')
    @stack('footerstyles')
    <script type="text/javascript">
        @stack('JsBeforeReady')
                $(document).ready(function(){
                    @stack('DocumentReady')
                });
    </script>
</body>
@endif
</html>