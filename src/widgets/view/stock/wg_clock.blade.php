@extends('fe_widgets::widgetFrame')

@section('Widget_contents')
    <div class="wg_clock m-t-30">
        <ul class="jquery-clock small" data-jquery-clock="">
            <li class="jquery-clock-pin"></li>
            <li class="jquery-clock-sec"></li>
            <li class="jquery-clock-min"></li>
            <li class="jquery-clock-hour"></li>
        </ul>
        @if ($DisableDigital===false)
            <div class="row wg_clock_digital">
                <div class="col-md-2"></div>
                <div class="col-md-8 col-sm-12 m-t-10">
                    <div class="row f-32 bold" style="font-family: fantasy;">
                        <div class="wg_hour col-md-4 p-0 text-center">
                            00
                        </div>
                        <div class="col-md-1 p-0 text-center c-gray" style="top: -5px;left: -3px;">:</div>
                        <div class="wg_min col-md-3 p-0 text-center">
                            00
                        </div>
                        <div class="col-md-1 p-0 text-center c-gray" style="top: -5px;">:</div>
                        <div class="wg_sec col-md-3 p-0 text-center">
                            00
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        @endif
    </div>
@overwrite