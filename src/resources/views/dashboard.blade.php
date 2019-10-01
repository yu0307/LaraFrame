@extends('felaraframe::layout')

@prepend('footerscripts')
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/SortableMaster/Sortable.min.js')}}"></script> <!-- Sortable  MANDATORY-->
<script type="text/javascript" src="{{asset('feiron/felaraframe/plugins/select2/dist/js/select2.full.min.js')}}"></script> <!-- Select2 -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/js/dashboard.js')}}"></script> <!-- DashBoard driver MANDATORY -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/widgets/WidgetAjax.js')}}"></script> <!-- Widget control script -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/flip-master/jquery.flip.min.js')}}"></script> <!-- Widget control script -->

@endprepend

@push('headerstyles')
<link href="{{asset('feiron/felaraframe/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('/feiron/felaraframe/css/dashboard.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endpush

@section('content')
<div class="widgetArea" id="fe_widgetArea">
    <div class="list-group" id="fe_widgetCtrls">
        @yield('Widget_Area')
        @php
            echo app()->WidgetManager->renderUserWidgets(Auth::user());
        @endphp      
    </div>

    @feModal([
        'modal_ID'=>'dashboardWidgetControl',
        'header'=>'Site Available Widgets',
        'modal_size'=>'',
        'header_bg'=>'dark',
        'footer'=>'
        <button type="button" class="btn btn-primary" id="widget_add">Add Widget</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        '
        ])
        <div id="fe_widget_list_area">
            <div id="fe_widget_list">
                <div class="text-center sm-col-12 m-t-10">
                    <i class="fa fa-spinner fa-spin fa-3x fa-fw loading"></i>
                    <div class="text-center ">Loading Site Widgets...</div>
                </div>
            </div>
            <div id="fe_widget_desc" class="f-18 p-10"></div>
        </div>
    @endfeModal
    <div id="new_widget_area" class="animated bd-9 c-gray fadeInUp fadeOutDown" style="z-index: 1000;">
        <div class="front text-center" id="widget_add">
            <div class="text-center m-5"><i class="fa fa-plus-circle fa-3x faa-float animated"></i></div>
            <h4 class="m-t-0"><strong>Add</strong> Widgets</h4>
        </div>
    </div>
</div>
@endsection

