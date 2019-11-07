@extends('felaraframe::layout')

@section('control_tab_contents')
    @php
        $menu='';
        $LF_CRUD='';
    @endphp
    @foreach (app()->frameOutlet->getOutlet('Fe_FrameOutlet') as $OutletItem)
        @php
            $ID=str_replace(' ','_',$OutletItem->MyName());
            $menu.='<li>
                        <a href="#'.$ID.'" data-toggle="tab">'.$OutletItem->MyName().'</a>
                    </li>';
        @endphp
        <div class="tab-pane fade" id="{{$ID}}">
            @php
                $view=$OutletItem->getView();
                if ($__env->exists($view->Name(),$view->getData())){
                        echo $__env->make($view->Name(),$view->getData())->render(); 
                }
            @endphp
        </div>
        @push('LF_CRUD')
            <div class="LF_CRUD row {{$ID}}" id="{{($ID.'_CRUD')}}">
                @yield($ID.'_CRUD')
            </div>
        @endpush
    @endforeach
@endsection

@push('OutletResource')
    {!! join(app()->frameOutlet->OutletResources('Fe_FrameOutlet')) !!}
@endpush

@push('footerscripts')
    <script type="text/javascript" src="{{asset('/feiron/felaraframe/js/controlpanel.js')}}"></script> <!-- controlpanel script -->
    @stack('OutletResource')
@endpush

@push('headerstyles')
<link href="{{asset('/feiron/felaraframe/css/controlpanel.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endpush

@section('content')
    @fePortlet([
        'id'=>'controlPanel',
        'headerBackground'=>'dark',
        'headerText'=>'<h3>Admin Control Panel</h3>'
        ])
        <ul class="nav nav-tabs nav-primary">
            <li class="active"><a href="#General" data-toggle="tab">General</a></li>
            {!!$menu!!}
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active in" id="General" data_target="{!!route('LF_controlpanel')!!}">
                <p class="light alert alert-info">General configuration goes here. sort of as a catch it all.</p>
                {!!app()->FeFrame->RenderSiteSettings()!!}
                <div class="clearfix"></div>
                <button class="btn btn-primary pull-right btn_site_settings_save">Update Site Settings</button>
            </div>
            @yield('control_tab_contents')
        </div>
        <div class="clear-fix"></div>
    @endfePortlet

    @feModal([
        'modal_ID'=>'control_CRUD',
        'footer'=>'
            <div class="buttonSlot col-md-8 col-sm-12">

            </div>
            <div class="col-md-4 col-sm-12">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
            
        '
        ])
        <div class="loading text-center">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
            <span><h4 class="text-center t-center">Loading Contents ...</h4></span>
        </div>
        <div class="CRUD_ctr_Area">
            <div class="LF_CRUD row" id="General_CRUD">
                
            </div>
            @stack('LF_CRUD')
        </div>
    @endfeModal

@endsection

