<div class="panel {{$class??''}}" {{empty($id)?'':"id=$id"}}>
    @isset($headerText)
    <div class="panel-header bg-{{$headerBackground??'primary'}} {{(($headercontrols??false)===true)?'panel-controls':''}}">
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