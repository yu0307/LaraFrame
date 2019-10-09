@extends('felaraframe::layout')
@section('control_tab_contents')
    @php
        $menu='';
    @endphp
    @foreach (app()->frameOutlet->getOutlet('Fe_FrameOutlet') as $OutletItem)
        @php
            $menu.='<li>
                        <a href="#'.str_replace(' ','_',$OutletItem->MyName()).'" data-toggle="tab">'.$OutletItem->MyName().'</a>
                    </li>';
        @endphp
        <div class="tab-pane fade" id="{{str_replace(' ','_',$OutletItem->MyName())}}">
            @php
                $view=$OutletItem->getView();
                if ($__env->exists($view->Name(),$view->getData())){
                        echo $__env->make($view->Name(),$view->getData(), \Illuminate\Support\Arr::except(get_defined_vars(), ["__data", "__path"]))->render(); 
                }
            @endphp
        </div>
    @endforeach
@endsection

@push('footerscripts')
    {!! join(app()->frameOutlet->OutletResources('Fe_FrameOutlet')) !!}
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
@endsection