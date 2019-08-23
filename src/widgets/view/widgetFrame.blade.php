<div class="col-md-{{$Width??'4'}} fe_widget">
    <div class="panel">
        <div class="panel-header {{(isset($DisableControls)?'':'panel-controls')}}  {{$HeaderBackground??'bg-primary'}}">
            <h3>
                <i class="fa fa-{{$HeaderIcon??'star'}}"></i>
                    @if(isset($Widget_header))
                        {!! $Widget_header !!}
                    @else
                        @yield('Widget_header')
                    @endif
                    
            </h3>
        </div>
        <div class="panel-content">
            <div class="withScroll" data-height="400">
                @if(!empty($Widget_contents))
                    {!! $Widget_contents !!}
                @else
                    @yield('Widget_contents')
                @endif
            </div>
        </div>
        <div class="panel-footer p-t-0 p-b-0 {{$FooterBackground??'bg-dark'}}">
            @if(isset($Widget_footer))
                {!! $Widget_footer !!}
            @else
                @yield('Widget_footer')
            @endif
        </div>
    </div>
</div>