@extends('felaraframe::layout')
@php
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/style.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/ui.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/select2/dist/css/select2.min.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/structure.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/themes/bootstrap-v3.css'),'headerstyles');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/css/usrProfile.css'),'headerstyles');

    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-3.1.0.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-migrate-3.0.0.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap/js/bootstrap.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-loading/lada.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/select2/dist/js/select2.full.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/icheck/icheck.min.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/application.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/plugins.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/laraframe.js'),'footerscripts');
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/global.js'),'footerscripts');
    
    foreach(app()->frameOutlet->OutletResources('Fe_FrameProfileOutlet') as $asset){
        app()->FeFrame->enqueueResource($asset,'OutletResource');
    }
@endphp

@section('Prof_control_tab_contents')
    @php
        $menu='';
    @endphp
    @if ($Editable)
        @foreach (app()->frameOutlet->getOutlet('Fe_FrameProfileOutlet') as $OutletItem)
            @php
                $active = empty($menu);
                $ID=str_replace(' ','_',$OutletItem->MyName());
                $menu.='<li '.($active?'class="active"':'').'>
                            <a href="#'.$ID.'"  data-toggle="tab">'.$OutletItem->MyName().'</a>
                        </li>';
            @endphp
            <div class="tab-pane fade h-100p {{$active?'active in':''}}" id="{{$ID}}">
                @include($OutletItem->getView()->getname())
            </div>
        @endforeach
    @endif
@endsection

@section('content')    
    @fePortlet([
        'id'=>'Profile_Panel',
        'class'=>'m-0 m-l-15 m-r-15 h-100p'
        ])
        @empty($User)
            <h4 class=" alert t-center text-center" >User not found ...</h4>
        @else 
            <div class="container-fluid h-100p">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body p-10">
                                <div class="text-center col-md-2 hidden-xs hidden-sm" style="min-height: 120px;">
                                    <div class="bd-full img-circle  usrProfile {{$Editable?'editable':''}}">
                                        <img src="{{app()->FeFrame->GetProfileImage(120)}}" class="img-lg img-thumbnail img-responsive img-circle bg-light" alt="avatar">
                                    </div>
                                </div>
                                <div class="clearfix col-md-10 col-sm-12">
                                    <h2 class="c-dark w-600">{{$User->title??$User->name}}</h2>
                                    <p class="c-gray f-16">{{$User->subtitle??''}}</p>
                                    <p class="c-gray">{{$User->subtext??''}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row h-80p">
                    <div class="col-md-12 p-10 h-100p">
                        <div class="tab_left h-100p">
                            <ul class="nav nav-tabs nav-primary h-100p f-16">
                                {!!$menu!!}
                            </ul>
                            <div class="tab-content h-100p">
                                @yield('Prof_control_tab_contents')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @yield('ExtraContents')
        @endempty
    @endfePortlet
@endsection

