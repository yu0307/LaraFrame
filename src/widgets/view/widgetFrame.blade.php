@if (false!==($AjaxLoad??false))
    @php
        unset($Ajax['AjaxJS']);
    @endphp
<script type="text/javascript">
    AjaxWidgetPool['{{$ID}}']=@json($Ajax)
</script>
@endif

@yield('Widget_InlineScript')

<div class="col-md-{{$Width??'4'}} {{$col}} fe_widget fe_widget_{{$Type}} " id="{{$ID}}" name="{{$WidgetName}}" usrKey="{{$usr_key}}">
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


@push('JsBeforeReady')
    @if ((!empty($usrSettings)===true) || (!empty($widgetConfig)===true))
        DashBoardWidgetBank['{{'wg_'.$usr_key}}']={
                        settings:@json($usrSettings??[]),
                        widgetConfig:@json($widgetConfig??[])};
    @endif
    @yield('Widget_JsBeforeReady');
@endpush

@php
    foreach ($headerscripts as $script){
        app()->FeFrame->enqueueResource($script['file'],'headerscripts');
    }
    foreach ($headerstyles as $script){
        app()->FeFrame->enqueueResource($script['file'],'headerstyles');
    }
    foreach ($footerscripts as $script){
        app()->FeFrame->enqueueResource($script['file'],'footerscripts');
    }
    foreach ($footerstyles as $script){
        app()->FeFrame->enqueueResource($script['file'],'footerstyles');
    }
@endphp

@push('DocumentReady')
    @yield('Widget_DocumentReady')
@endpush