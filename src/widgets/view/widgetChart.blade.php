@extends('fe_widgets::widgetFrame')

@section('Widget_contents')

<div id="wg_chart_{{$ID}}" wg_id="{{$ID}}" class="wg_Charts fe_widget_WidgetChart" style="height: 100%;" chartType="{{$chartType??"line"}}"></div>

@overwrite

