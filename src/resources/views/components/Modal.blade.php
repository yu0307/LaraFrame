<div {{$attributes->merge(['class' => "modal fade dashmodal modal-".($direction??'top')])}} tabindex="-1" role="dialog" >
  <div class="modal-dialog {{$size??'modal-lg'}} modal-{{$direction??'top'}}" role="document">
    <div class="modal-content">
        {!!($action??false)? ('<form action="'.($action??'#').'" name="'.($name??'myform').'" '.(($formId??false)?('id="'.$formId.'"'):'').' method="'.($method??'post').'">'):''!!}
        <div class="modal-header bg-{{ $headerBg??'primary' }}">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $header??'' }}</h4>
        </div>
        <div class="modal-body">

            {{$slot}}

        </div>
        <div class="modal-footer bg-{{ $footerBg??'gray-light' }} ">
            @if(isset($footer))
                {!! $footer??'' !!}
            @else
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            @endif
        </div>
        {!!($action??false)?'</form>':''!!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->