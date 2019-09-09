@extends('fe_widgets::widgetFrame')

@pushonce('footerscripts',$Type)
    <script src="{{asset('/feiron/felaraframe/plugins/skycons/skycons.min.js')}}"></script> <!-- Animated Weather Icons -->
    <script src="{{asset('/feiron/felaraframe/plugins/simplerWeather/jquery.simplerWeather.min.js')}}"></script> <!-- Weather Plugin -->
    <script type="text/javascript" src="{{asset('/feiron/felaraframe/widgets/wg_weather.js')}}"></script>
@endpushonce

@section('Widget_contents')
<div class="wg_weather">
    <div class="panel widget-weather bg-transparent m-b-0"></div>
</div>
@overwrite