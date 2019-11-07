@prepend('footerscripts')
<script src="{{asset('/feiron/felaraframe/plugins/jquery/jquery-3.1.0.min.js')}}"></script>
<script src="{{asset('/feiron/felaraframe/plugins/jquery/jquery-migrate-3.0.0.min.js')}}"></script>
<script src="{{asset('/feiron/felaraframe/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{asset('/feiron/felaraframe/plugins/gsap/main-gsap.min.js')}}"></script> <!-- HTML Animations -->
<script src="{{asset('/feiron/felaraframe/plugins/jquery-block-ui/jquery.blockUI.min.js')}}"></script> <!-- simulate synchronous behavior when using AJAX -->
<script src="{{asset('/feiron/felaraframe/plugins/bootbox/bootbox.min.js')}}"></script>
<script src="{{asset('/feiron/felaraframe/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script> <!-- Custom Scrollbar sidebar -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap-dropdown/bootstrap-hover-dropdown.min.js')}}"></script> <!-- Show Dropdown on Mouseover -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap-progressbar/bootstrap-progressbar.min.js')}}"></script> <!-- Animated Progress Bar -->
<script src="{{asset('/feiron/felaraframe/plugins/switchery/switchery.min.js')}}"></script> <!-- IOS Switch -->
<script src="{{asset('/feiron/felaraframe/plugins/retina/retina.min.js')}}"></script> <!-- Retina Display -->
<script src="{{asset('/feiron/felaraframe/plugins/jquery-cookies/jquery.cookies.js')}}"></script> <!-- Jquery Cookies, for theme -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap/js/jasny-bootstrap.min.js')}}"></script> <!-- File Upload and Input Masks -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap-tags-input/bootstrap-tagsinput.min.js')}}"></script> <!-- Select Inputs -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap-loading/lada.min.js')}}"></script> <!-- Buttons Loading State -->
<script src="{{asset('/feiron/felaraframe/plugins/timepicker/jquery-ui-timepicker-addon.min.js')}}"></script> <!-- Time Picker -->
<script src="{{asset('/feiron/felaraframe/plugins/multidatepicker/multidatespicker.min.js')}}"></script> <!-- Multi dates Picker -->
<script src="{{asset('/feiron/felaraframe/plugins/touchspin/jquery.bootstrap-touchspin.min.js')}}"></script> <!-- A mobile and touch friendly input spinner component for Bootstrap -->
<script src="{{asset('/feiron/felaraframe/plugins/autosize/autosize.min.js')}}"></script> <!-- Textarea autoresize -->
<script src="{{asset('/feiron/felaraframe/plugins/icheck/icheck.min.js')}}"></script> <!-- Icheck -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap-context-menu/bootstrap-contextmenu.min.js')}}"></script> <!-- File Upload and Input Masks -->
<script src="{{asset('/feiron/felaraframe/plugins/slick/slick.min.js')}}"></script> <!-- Slider -->
<script src="{{asset('/feiron/felaraframe/plugins/countup/countUp.min.js')}}"></script> <!-- Animated Counter Number -->
<script src="{{asset('/feiron/felaraframe/plugins/noty/noty.min.js')}}"></script> <!-- Notifications -->
<script src="{{asset('/feiron/felaraframe/plugins/backstretch/backstretch.min.js')}}"></script> <!-- Background Image -->
<script src="{{asset('/feiron/felaraframe/plugins/bootstrap-slider/bootstrap-slider.js')}}"></script> <!-- Bootstrap Input Slider -->
<script src="{{asset('/feiron/felaraframe/plugins/visible/jquery.visible.min.js')}}"></script> <!-- Visible in Viewport -->
<script src="{{asset('/feiron/felaraframe/js/widgets/notes.js')}}"></script> <!-- Notes Script -->
<script src="{{asset('/feiron/felaraframe/js/quickview.js')}}"></script> <!-- Quickview Script -->
@if (($siteInfo['themeSettings']['sb_showon']??'')=='Hover')
    <script src="{{asset('/feiron/felaraframe/js/sidebar_hover.js')}}"></script>
@endif
<script src="{{asset('/feiron/felaraframe/js/layoutAPI.js')}}"></script>
<script src="{{asset('/feiron/felaraframe/js/application.js')}}"></script> <!-- Main Application Script -->
<script src="{{asset('/feiron/felaraframe/js/plugins.js')}}"></script> <!-- Main Plugin Initialization Script -->
<script src="{{asset('/feiron/felaraframe/js/laraframe.js')}}"></script> <!-- Main Plugin Initialization Script -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/js/global.js')}}"></script> <!-- global js -->
@endprepend

@prepend('footerstyles')

@endprepend


@section('footer')
<div class="footer m-0 p-0">
    <div class="copyright p-0">
        <p class="pull-right sm-pull-reset m-b-0"> <span>Copyright <span class="copyright">©</span> {{date('Y')}} </span> <span>{{config('app.name')}}</span>. <span>All rights reserved. </span> </p>
    </div>
</div>
@endsection