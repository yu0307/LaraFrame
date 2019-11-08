@php
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-3.1.0.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery/jquery-migrate-3.0.0.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/gsap/main-gsap.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-block-ui/jquery.blockUI.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootbox/bootbox.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap/js/bootstrap.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-dropdown/bootstrap-hover-dropdown.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-progressbar/bootstrap-progressbar.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/switchery/switchery.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/retina/retina.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/jquery-cookies/jquery.cookies.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap/js/jasny-bootstrap.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-tags-input/bootstrap-tagsinput.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-loading/lada.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/timepicker/jquery-ui-timepicker-addon.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/multidatepicker/multidatespicker.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/touchspin/jquery.bootstrap-touchspin.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/autosize/autosize.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/icheck/icheck.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-context-menu/bootstrap-contextmenu.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/slick/slick.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/countup/countUp.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/noty/noty.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/backstretch/backstretch.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/bootstrap-slider/bootstrap-slider.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/plugins/visible/jquery.visible.min.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/widgets/notes.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/quickview.js'),'footerscripts',true);
    if(($siteInfo['themeSettings']['sb_showon']??'')=='Hover'){
        app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/sidebar_hover.js'),'footerscripts',true);
    }
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/layoutAPI.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/application.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/plugins.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/laraframe.js'),'footerscripts',true);
    app()->FeFrame->enqueueResource(asset('/feiron/felaraframe/js/global.js'),'footerscripts',true);
@endphp

@section('footer')
<div class="footer m-0 p-0">
    <div class="copyright p-0">
        <p class="pull-right sm-pull-reset m-b-0"> <span>Copyright <span class="copyright">©</span> {{date('Y')}} </span> <span>{{config('app.name')}}</span>. <span>All rights reserved. </span> </p>
    </div>
</div>
@endsection