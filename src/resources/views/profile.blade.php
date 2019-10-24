@extends('felaraframe::layout')

@section('Prof_control_tab_contents')
    @php
        $menu='';
    @endphp
    @if ($Editable)
        @foreach (app()->frameOutlet->getOutlet('Fe_FrameProfileOutlet') as $OutletItem)
            @php
                $ID=str_replace(' ','_',$OutletItem->MyName());
                $menu.='<li>
                            <a href="#'.$ID.'" data-toggle="tab">'.$OutletItem->MyName().'</a>
                        </li>';
            @endphp
            <div class="tab-pane fade h-100p" id="{{$ID}}">
                @include($OutletItem->getView()->getname())
            </div>
        @endforeach
    @endif
@endsection

@push('Prof_OutletResource')
    {!! join(app()->frameOutlet->OutletResources('Fe_FrameProfileOutlet')) !!}
@endpush

@push('footerscripts')
    @stack('Prof_OutletResource')
@endpush

@push('headerstyles')
    <link href="{{asset('/feiron/felaraframe/css/usrProfile.css')}}" rel="stylesheet"> <!-- MANDATORY -->
@endpush

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
                                <li class="active"><a href="#usrProfHome" data-toggle="tab">General</a></li>
                                {!!$menu!!}
                            </ul>
                            <div class="tab-content h-100p">
                                <div class="tab-pane fade active in h-100p" id="usrProfHome">
                                    <p class="light">Display some useful information here.</p>
                                </div>
                                @yield('Prof_control_tab_contents')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endempty
    @endfePortlet
    
@endsection

