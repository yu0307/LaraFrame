<div class="modal fade {{$modal_class??''}} modal-{{($modal_direction??false)?$modal_direction:'top'}}" tabindex="-1" role="dialog" {!!isset($modal_ID)?('id="'.$modal_ID.'"'):''!!}>
  <div class="modal-dialog {{$modal_size??'modal-lg'}} modal-{{($modal_direction??false)?$modal_direction:'top'}}" role="document">
    <div class="modal-content">
        {!!($has_form??false)? ('<form action="'.($form_action??'#').'" name="'.($form_name??'myform').'" '.(($form_id??false)?('id="'.$form_id.'"'):'').' method="'.($form_method??'post').'">'):''!!}
        <div class="modal-header bg-{{ $header_bg??'primary' }}">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">{{ $header??'' }}</h4>
        </div>
        <div class="modal-body">

            {{$slot}}

        </div>
        <div class="modal-footer bg-{{ $footer_bg??'gray-light' }} ">
            @if(isset($footer))
                {!! $footer !!}
            @else
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            @endif
        </div>
        {!!($has_form??false)?'</form>':''!!}
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->