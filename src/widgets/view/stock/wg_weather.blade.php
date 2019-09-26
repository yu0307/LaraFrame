@extends('fe_widgets::widgetFrame')

@section('Widget_contents')
<div class="wg_weather">
<div class="panel widget-weather bg-transparent m-b-0" city="{{$location??'Mountain View, US'}}" units="{{$unit??'Imperial'}}"></div>
</div>
@overwrite