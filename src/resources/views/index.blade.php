@includeIf('felaraframe::topbar')

<body class="felaraframe {{($siteInfo['themeSettings']['page_display']??'')=='Boxed'?'boxed':''}} bg-{{strtolower($siteInfo['themeSettings']['page_bgcolor']??'Light-blue')}} {{($siteInfo['themeSettings']['sb_structure']??'')=='Light'?'sidebar-light theme-sltl':('sidebar-'.(strtolower(trim($siteInfo['themeSettings']['sb_structure']??'normal'))))}} {{($siteInfo['themeSettings']['sb_style']??'Fixed')=='Fixed'?'fixed-sidebar':''}} {{($siteInfo['themeSettings']['sb_subshowon']??'')=='Hover'?'submenu-hover':''}} {{($siteInfo['themeSettings']['sb_showon']??'')=='Hover'?('sidebar-hover '.((($siteInfo['themeSettings']['sb_style']??'')=='Fixed')?'':'fixed-sidebar')):''}} {{($siteInfo['themeSettings']['sb_initbh']??'')=='Collapsed'?'sidebar-collapsed':''}} color-{{($siteInfo['themeSettings']['page_color']??'default')=='Dark'?'default':(strtolower(trim($siteInfo['themeSettings']['page_color']??'default'))??'default')}} {{($siteInfo['themeSettings']['tb_location']??'Fixed')=='Fixed'?'fixed-topbar':'fluid-topbar'}}
    @switch(($siteInfo['themeSettings']['page_template']??'Dark 1'))
        @case('Dark 2')
            theme-sdtd
        @break
        @case('Light 1')
            theme-sltd
        @break
        @case('Light 2')
            theme-sltl
        @break
        @default
            theme-sdtl
    @endswitch
">
    <section>
        @yield('sidebar')
        <!-- BEGIN MAIN CONTENT -->
        <div class="main-content">
            @yield('topbar')
            <div class="page-content">
                <!-- PAGE CONTENT -->
                @yield('content')
            </div>
            @yield('footer')
        </div>
        <!-- END MAIN CONTENT -->
    </section>

    <div class="loader-overlay">
        <!-- PRELOADER -->
    </div>

    <div id="quickview-sidebar" class="sideview">
        <div class="quickview-header">
            @yield('quickview_header')
        </div>
        <div class="quickview">
            <div class="tab-content">
                <!-- SIDEBAR -->
                @yield('sidebar_alt')
            </div>
            <div class="quickview_footer">
                @yield('sidebar_alt_footer')
            </div>
        </div>
    </div>    

    @stack('footerscripts')
    @stack('footerstyles')
    <script type="text/javascript">
        @stack('JsBeforeReady')
        $(document).ready(function(){
            @stack('DocumentReady')
        });
    </script>
</body>
