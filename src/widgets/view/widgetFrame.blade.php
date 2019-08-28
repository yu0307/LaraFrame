@if (false!==($AjaxLoad??false))

    @pushonce('footerscripts',$Type)
        <script type="text/javascript" src="{{$Ajax['AjaxJS']}}"></script>
    @endpushonce
    @php
        unset($Ajax['AjaxJS']);
    @endphp
    @push('footerscripts')
            <script type="text/javascript">
                WidgetPool['{{$ID}}']=@json($Ajax)
            </script>
    @endpush
@endif

<div class="col-md-{{$Width??'4'}} fe_widget fe_widget_{{$Type}} " id="{{$ID}}">
    <div class="panel">
        <div class="panel-header {{(($DisableControls??false)?'':'panel-controls')}}  {{$HeaderBackground??'bg-primary'}}">
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
            <div class="withScroll" data-height="{{$DataHeight??'400'}}">
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