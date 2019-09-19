@pushonce('footerscripts','DatePicker')
<script type="text/javascript" src="{{asset('feiron/felaraframe/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}">
</script> <!-- datepicker -->
@endpushonce

@pushonce('headerstyles','DatePicker')
<link type="text/css" href="{{asset('feiron/felaraframe/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<!-- datepicker -->
@endpushonce

@php
    $id=$id??('datepicker_'.rand(100,999));
    $value=$value??false;
@endphp

<div class="form-group">
    <label class="form-label">{{$label??''}}</label>
    <div class="input-group date" data-provide="datepicker">
        <input type="text" class="form-control" id="{{ $id }}" value="{{($value===true)?date('m/d/Y'):($value===false?'':$value)}}">
        <div class="input-group-addon">
            <span class="fa fa-calendar" aria-hidden="true"></span>
        </div>
    </div>
</div>
