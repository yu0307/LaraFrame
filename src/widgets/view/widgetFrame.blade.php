@if (false!==($AjaxLoad??false))
    @php
        unset($Ajax['AjaxJS']);
    @endphp
<script type="text/javascript">
    AjaxWidgetPool['{{$ID}}']=@json($Ajax)
</script>
@endif

@yield('Widget_InlineScript')

<div class="col-md-{{$Width??'4'}} fe_widget fe_widget_{{$Type}} " id="{{$ID}}" name="{{$WidgetName}}" usrKey="{{$usr_key}}">
    <div class="panel {{$WidgetBackground??'bg-white'}}">
            @if (true!==$DisableHeader)
                <div class="panel-header {{(($DisableControls??false)?'':'panel-controls')}}  {{$HeaderBackground??'bg-primary'}} {{((empty($usrSettings)===true)?'':'HasSettingOutlet')}}">
                    <h3>
                        <i class="fa fa-{{$HeaderIcon??'star'}}"></i>
                        @if(isset($Widget_header))
                        {!! $Widget_header !!}
                        @else
                        @yield('Widget_header')
                        @endif
                    </h3>
                </div>
            @endif
        <div class="panel-content">
            <div class="withScroll wg_main_cnt" data-height="{{$DataHeight??'400'}}">
                @if(!empty($Widget_contents))
                {!! $Widget_contents !!}
                @else
                @yield('Widget_contents')
                @endif
            </div>
        </div>
        @if (true!==$DisableFooter)
            <div class="panel-footer p-t-0 p-b-0 {{$FooterBackground??'bg-dark'}}">
                @if(isset($Widget_footer))
                {!! $Widget_footer !!}
                @else
                @yield('Widget_footer')
                @endif
            </div>
        @endif
    </div>
</div>


@push('headerscripts')
    @foreach ($headerscripts as $script)
        <script type="text/javascript" src="{{$script['file']}}"></script>
    @endforeach
@endpush
@push('headerstyles')
    @foreach ($headerstyles as $style)
        <link href="{{$style['file']}}" rel="stylesheet"> <!-- MANDATORY -->
    @endforeach
@endpush
@push('footerscripts')
    @foreach ($footerscripts as $script)
        <script type="text/javascript" src="{{$script['file']}}"></script>
    @endforeach
@endpush
@push('footerstyles')
    @foreach ($footerstyles as $style)
        <link href="{{$style['file']}}" rel="stylesheet"> <!-- MANDATORY -->
    @endforeach
@endpush

@push('DocumentReady')
    @if ((!empty($usrSettings)===true))
        DashBoardWidgetBank['{{'wg_'.$usr_key}}']={
                        settings:@json($usrSettings)
                    };
    @endif
    @yield('Widget_DocumentReady')
@endpush