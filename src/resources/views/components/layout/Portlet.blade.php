<div class="panel portlet {{$class??''}}" {{empty($id)?'':"id=$id"}} {!!empty($attr)?'':$attr!!}>
    @isset($headerText)
    <div class="panel-header bg-{{$headerBackground??'dark'}} {{(($headercontrols??false)===true)?'panel-controls':''}}">
        {!!$headerText??''!!}
    </div>
    @endisset

    <div class="panel-content">
        {{ $slot }}
    </div>
    
    @isset($footerText)
    <div class="panel-footer bg-{{$footerBackground??'none'}}">
        {!!$footerText??''!!} 
    </div>
    @endisset
</div>