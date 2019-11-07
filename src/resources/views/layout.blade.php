<!DOCTYPE html>
<html lang="en">
@includeIf($siteInfo['theme'].'::header')
@includeIf($siteInfo['theme'].'::footer')
@includeIf($siteInfo['theme'].'::sidebar')

@if(($siteInfo['theme']!='felaraframe') && isset($siteInfo['Setting']['tm_force_bootstrap']))    

    @if (in_array('fontAwesome',$siteInfo['Setting']['tm_force_bootstrap']))
        @push('headerstyles')
            <link href="{{asset('/feiron/felaraframe/css/icons/font-awesome/font-awesome.min.css')}}" rel="stylesheet"> <!-- MANDATORY -->
        @endpush
    @endif

    @if (in_array('Bootstrap',$siteInfo['Setting']['tm_force_bootstrap']))
        @prepend('footerscripts')
            <script src="{{asset('/feiron/felaraframe/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
        @endprepend
        @prepend('headerstyles')
            <link href="{{asset('/feiron/felaraframe/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"> <!-- MANDATORY -->
        @endprepend
    @endif

    @if (in_array('JqueryUI',$siteInfo['Setting']['tm_force_bootstrap']))
        @prepend('footerscripts')
            <script src="{{asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
        @endprepend
        @prepend('headerstyles')
            <link href="{{asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.css')}}" rel="stylesheet"> <!-- MANDATORY -->
        @endprepend
    @endif

    @if (in_array('Jquery',$siteInfo['Setting']['tm_force_bootstrap']))
        @prepend('footerscripts')
            <script src="{{asset('/feiron/felaraframe/plugins/jquery/jquery-3.1.0.min.js')}}"></script>
            <script src="{{asset('/feiron/felaraframe/plugins/jquery/jquery-migrate-3.0.0.min.js')}}"></script>
            <script type="text/javascript" src="{{asset('/feiron/felaraframe/js/global.js')}}"></script> <!-- global js -->
        @endprepend
    @endif
@endif

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