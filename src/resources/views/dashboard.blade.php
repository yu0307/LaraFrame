@extends('felaraframe::layout')

@prepend('footerscripts')
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/SortableMaster/Sortable.min.js')}}"></script> <!-- Sortable  MANDATORY-->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/js/dashboard.js')}}"></script> <!-- DashBoard driver MANDATORY -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/widgets/WidgetAjax.js')}}"></script> <!-- Widget control script -->
@endprepend

@push('headerstyles')
<link href="{{asset('/feiron/felaraframe/css/dashboard.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endpush

@section('content')
<div class="widgetArea" id="fe_widgetArea">
    <div class="list-group" id="fe_widgetCtrls">
        @yield('Widget_Area')

        <div id="new_widget_area" class="bd-9 c-gray col-md-3 col-sm-6 m-t-30 m-b-30 p-20">
            <div class="panel panel-transparent p-t-20 p-b-20 m-0 m-t-20 m-b-20 f-16" >
                <div class="panel-content widget_control_win" id="widget_add_win">
                    <div class="inner">
                        <div class="front text-center" id="widget_add">
                            <h1><strong>Add</strong> Widgets</h1>
                            <div class="text-center m-10"><i class="fa fa-plus-circle fa-4x faa-vertical animated-hover"></i></div>
                            Explore and add new widgets to the dashboard.
                        </div>
                        <div class="back">
                            <h1>John Doe</h1>
                            <p>Architect & Engineer</p>
                            <p>We love that guy</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection