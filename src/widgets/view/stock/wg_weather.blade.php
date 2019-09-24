@extends('fe_widgets::widgetFrame')

@pushonce('footerscripts',$Type)
    <script src="{{asset('/feiron/felaraframe/plugins/skycons/skycons.min.js')}}"></script> <!-- Animated Weather Icons -->
    <script src="{{asset('/feiron/felaraframe/plugins/FeiWeather/FeiWeather.js')}}"></script> <!-- Weather Plugin -->
    <script type="text/javascript" src="{{asset('/feiron/felaraframe/widgets/wg_weather.js')}}"></script>
@endpushonce

@pushonce('headerstyles',$Type)
    <link href="{{asset('/feiron/felaraframe/widgets/css/wg_Weather.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endpushonce

@section('Widget_contents')
<div class="wg_weather">
<div class="panel widget-weather bg-transparent m-b-0" city="{{$location??'Mountain View, US'}}" units="{{$unit??'Imperial'}}"></div>
</div>
@overwrite