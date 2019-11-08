@php
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/style.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/theme.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/ui.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/extension.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/themes/bootstrap-v3.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/animation-css/animate.min.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/font-awesome-animation/font-awesome-animation.min.css'),'headerstyles',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/structure.css'),'headerstyles',true);
@endphp
@prepend('headerscripts')
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="{{asset('/felaraframe/assets/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js')}}"></script>
<![endif]-->
@endprepend

@prepend('headerstyles')
<link href="https://fonts.googleapis.com/css?family=Nothing+You+Could+Do" rel="stylesheet" type="text/css">
@endprepend

@section('header')
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="description" content="@yield('site_description','')">
<meta name="author" content="@yield('site_author','Lucas F, Lu')">
<link rel="shortcut icon" href="@yield('favicon',asset('/feiron/felaraframe/images/favicon.png'))" type="image/png">
@endsection