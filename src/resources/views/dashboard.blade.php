@extends('felaraframe::layout')
@php
    $SiteWidgets=[];
    $options='';
    foreach(app()->WidgetManager->getSiteWidgetList() as $widget=>$settings){
        $options.="<option value='$widget'>$widget</option>";
        $SiteWidgets[$widget]=$settings['Description'];
    }
@endphp

@prepend('footerscripts')
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/SortableMaster/Sortable.min.js')}}"></script> <!-- Sortable  MANDATORY-->
<script type="text/javascript" src="{{asset('feiron/felaraframe/plugins/select2/dist/js/select2.full.min.js')}}"></script> <!-- Select2 -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/js/dashboard.js')}}"></script> <!-- DashBoard driver MANDATORY -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/widgets/WidgetAjax.js')}}"></script> <!-- Widget control script -->

@endprepend

@push('headerstyles')
<link href="{{asset('feiron/felaraframe/plugins/select2/dist/css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('/feiron/felaraframe/css/dashboard.css')}}" rel="stylesheet"> <!-- MANDATORY -->
<script type="text/javascript">
    var SiteWidgets=@json($SiteWidgets);
</script>
@endpush

@section('content')
<div class="widgetArea" id="fe_widgetArea">
    <div class="list-group" id="fe_widgetCtrls">
        @yield('Widget_Area')
        @php
            echo app()->WidgetManager->renderUserWidgets(Auth::user());
        @endphp      
    </div>
    
    <div id="shadow_widget" class="col-md-3 animated fadeIn fadeOutUp faa-fast">
        <div class="panel">
            <div class="panel-header bg-default">
                <h3>
                    <i class="fa fa-list"></i>
                    <strong>New</strong> Widget
                </h3>
            </div>
            <div class="panel-content p-t-0">
                <div class="withScroll" data-height="300">
                    <h3><strong>Select</strong> your widget from the list below</h3>
                    <select class="btn-block" name="site_widgets" id="site_widgets" style="width:100%">
                        <option value=""></option>
                        {!!$options!!}
                    </select>
                    <div class="widget_description p-10">
    
                    </div>
                </div>
            </div>
            <div class="panel-footer p-10 bg-default">
                <button class="btn btn-success btn-sm pull-left btn-save">Add Widget</button>
                <button class="btn btn-danger btn-sm pull-right btn-cancel">Cancel</button>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div id="new_widget_area" class="animated bd-9 c-gray fadeInUp fadeOutDown">
        <div class="front text-center" id="widget_add">
            <div class="text-center m-5"><i class="fa fa-plus-circle fa-3x faa-float animated"></i></div>
            <h4 class="m-t-0"><strong>Add</strong> Widgets</h4>
        </div>
    </div>
</div>
@endsection

