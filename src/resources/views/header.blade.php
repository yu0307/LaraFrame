@prepend('headerscripts')
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="{{asset('/felaraframe/assets/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js')}}"></script>
<![endif]-->
@endprepend

@prepend('headerstyles')
<link href="http://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet" type="text/css">
<link href="{{asset('/feiron/felaraframe/css/style.css')}}" rel="stylesheet"> <!-- MANDATORY -->
<link href="{{asset('/feiron/felaraframe/css/theme.css')}}" rel="stylesheet"> <!-- MANDATORY -->
<link href="{{asset('/feiron/felaraframe/css/ui.css')}}" rel="stylesheet"> <!-- MANDATORY -->
<link href="{{asset('/feiron/felaraframe/css/extension.css')}}" rel="stylesheet"> <!-- MANDATORY -->
<link href="{{asset('/feiron/felaraframe/plugins/noty/noty.css/')}}" rel="stylesheet"> <!-- MANDATORY -->
<link href="{{asset('/feiron/felaraframe/plugins/noty/themes/bootstrap-v3.css/')}}" rel="stylesheet"> <!-- MANDATORY -->
<link href="{{asset('/feiron/felaraframe/css/structure.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endprepend

@section('header')
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="description" content="@yield('site_description','')">
<meta name="author" content="@yield('site_author','')">
<link rel="shortcut icon" href="@yield('favicon',asset('/feiron/felaraframe/images/favicon.png'))" type="image/png">
@endsection