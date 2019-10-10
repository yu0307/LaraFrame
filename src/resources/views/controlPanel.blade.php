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
                        echo $__env->make($view->Name(),$view->getData(), \Illuminate\Support\Arr::except(get_defined_vars(), ["__data", "__path"]))->render(); 
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

@push('footerscripts')
    <script type="text/javascript" src="{{asset('/feiron/felaraframe/js/controlpanel.js')}}"></script> <!-- controlpanel script -->
    {!! join(app()->frameOutlet->OutletResources('Fe_FrameOutlet')) !!}
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
            <div class="tab-pane fade active in" id="General">
                <p class="light">General configuration goes here. sort of as a catch it all.</p>
            </div>
            @yield('control_tab_contents')
        </div>
    @endfePortlet

    @feModal([
        'modal_ID'=>'control_CRUD'
        ])
        <div class="LF_CRUD row" id="General_CRUD">
            
        </div>
        @stack('LF_CRUD')
    @endfeModal
@endsection

