@extends('felaraframe::layout')
@php
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/style.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/ui.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/themes/bootstrap-v3.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/controlpanel.css'),'headerstyles');

    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-3.1.0.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-migrate-3.0.0.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap/js/bootstrap.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/icheck/icheck.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/select2/dist/js/select2.full.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/select2/dist/css/select2.min.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/application.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/plugins.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/laraframe.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/global.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/controlpanel.js'),'footerscripts');

    foreach(app()->frameOutlet->OutletResources('Fe_FrameOutlet') as $asset){
        app()->FeFrame->enqueueResource($asset,'OutletResource');
    }
@endphp
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

@section('content')
    <x-fe-portlet
        id="controlPanel"
        header-bg="dark"
    >
        <x-slot name="header">
            <h3>Admin Control Panel</h3>
        </x-slot>
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
                <div class="clearfix"></div>
            </div>
            @yield('control_tab_contents')
        </div>
        <div class="clear-fix"></div>
    </x-fe-portlet>

    <x-fe-modal id="control_CRUD" >
        <x-slot name="footer">
            <div class="buttonSlot col-md-8 col-sm-12">

            </div>
            <div class="col-md-4 col-sm-12">
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
            </div>
        </x-slot>

        <div class="loading text-center">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
            <span><h4 class="text-center t-center">Loading Contents ...</h4></span>
        </div>
        <div class="CRUD_ctr_Area">
            <div class="LF_CRUD row" id="General_CRUD">
                
            </div>
            @stack('LF_CRUD')
        </div>
    </x-fe-modal>

@endsection

