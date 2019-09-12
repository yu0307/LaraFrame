@extends('fe_widgets::widgetFrame')

@pushonce('footerscripts',$Type)
    <script type="text/javascript" src="{{asset('/feiron/felaraframe/widgets/wg_calendar.js')}}"></script>
@endpushonce

@pushonce('headerstyles',$Type)
    <link href="{{asset('/feiron/felaraframe/widgets/css/wg_calendar.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endpushonce

@section('Widget_contents')
<div class="wg_calendar">
    <div class="wg_calendar-header"></div>
</div>
@overwrite