/* ============================================================
 * Layout Script
 =========================================================== */
var $logopanel = $('.logopanel');
var $topbar = $('.topbar');
var $sidebar = $('.sidebar');
var $sidebarFooter = $('.sidebar-footer');

/****  Initiation of Main Functions  ****/
$(document).ready(function () {
    if ($('body').hasClass('sidebar-hover')) sidebarHover();

    $('[data-toggle]').on('click', function (event) {
        event.preventDefault();
        var toggleLayout = $(this).data('toggle');
        if (toggleLayout == 'sidebar-behaviour') toggleSidebar();
        if (toggleLayout == 'submenu') toggleSubmenuHover();
        if (toggleLayout == 'sidebar-collapsed') collapsedSidebar();
        if (toggleLayout == 'sidebar-top') toggleSidebarTop();
        if (toggleLayout == 'sidebar-hover') toggleSidebarHover();
        if (toggleLayout == 'topbar') toggleTopbar();
    });

});

/* ==========================================================*/
/* LAYOUTS API                                                */
/* ========================================================= */

/* Create Sidebar Fixed */
function handleSidebarFixed() {
    // removeSidebarHover();
    $('#switch-sidebar').prop('checked', true);
    $('#switch-submenu').prop('checked', false);
    $.removeCookie('submenu-hover');
    if ($('body').hasClass('sidebar-top')) {
        $('body').removeClass('fixed-topbar').addClass('fixed-topbar');
        $.removeCookie('fluid-topbar');
        $('#switch-topbar').prop('checked', true);
    }
    $('body').removeClass('fixed-sidebar').addClass('fixed-sidebar');
    $('.sidebar').height('');
    if (!$('body').hasClass('sidebar-collapsed')) removeSubmenuHover();
    createSideScroll();
    $.removeCookie('fluid-sidebar');
    $.cookie('fixed-sidebar', 1);
}

/* Create Sidebar Fluid / Remove Sidebar Fixed */
function handleSidebarFluid() {
    $('#switch-sidebar').prop('checked', false);
    if ($('body').hasClass('sidebar-hover')) {
        removeSidebarHover();
        $('#switch-sidebar-hover').prop('checked', false);
    }
    $('body').removeClass('fixed-sidebar');
    destroySideScroll();
    $.removeCookie('fixed-sidebar');
    $.cookie('fluid-sidebar', 1);
    $.cookie('fluid-sidebar', 1);
}

/* Toggle Sidebar Fixed / Fluid */
function toggleSidebar() {
    if ($('body').hasClass('fixed-sidebar')) handleSidebarFluid();
    else handleSidebarFixed();
}

/* Create Sidebar on Top */
function createSidebarTop() {
    $('#switch-sidebar-top').prop('checked', true);
    removeSidebarHover();
    $('body').removeClass('sidebar-collapsed');
    $.removeCookie('sidebar-collapsed');
    $('body').removeClass('sidebar-top').addClass('sidebar-top');
    $('.main-content').css('margin-left', '').css('margin-right', '');
    $('.topbar').css('left', '').css('right', '');
    if ($('body').hasClass('fixed-sidebar') && !$('body').hasClass('fixed-topbar')) {
        $('body').removeClass('fixed-topbar').addClass('fixed-topbar');
        $.removeCookie('fluid-topbar');
        $('#switch-topbar').prop('checked', true);
    }
    $('.sidebar').height('');
    destroySideScroll();
    $('#switch-sidebar-hover').prop('checked', false);
    $.cookie('sidebar-top', 1);
    $.removeCookie('sidebar-hover');
    $('.layout-option-sidebar-fixed, .layout-option-sidebar-hover, .layout-option-submenu-hover').hide(0);
}

/* Remove Sidebar on Top */
function removeSidebarTop() {
    $('#switch-sidebar-top').prop('checked', false);
    $('body').removeClass('sidebar-top');
    createSideScroll();
    $('#switch-sidebar-top').prop('checked', false);
    $.removeCookie('sidebar-top');
    $('.layout-option-sidebar-fixed, .layout-option-sidebar-hover, .layout-option-submenu-hover').show(0);
}

/* Toggle Sidebar on Top */
function toggleSidebarTop() {
    if ($('body').hasClass('sidebar-top')) removeSidebarTop();
    else createSidebarTop();
}

/* Create Sidebar only visible on Hover */
function createSidebarHover() {
    $('body').addClass('sidebar-hover');
    $('body').removeClass('fixed-sidebar').addClass('fixed-sidebar');
    $('.main-content').css('margin-left', '').css('margin-right', '');
    $('.topbar').css('left', '').css('right', '');
    $('body').removeClass('sidebar-top');
    removeSubmenuHover();
    removeCollapsedSidebar();
    sidebarHover();
    handleSidebarFixed();
    $('#switch-sidebar-hover').prop('checked', true);
    $('#switch-sidebar').prop('checked', true);
    $('#switch-sidebar-top').prop('checked', false);
    $.removeCookie('sidebar-hover');
    $.removeCookie('submenu-hover');
    $.cookie('sidebar-top', 1);
}

/* Remove Sidebar on Hover */
function removeSidebarHover() {
    $('#switch-sidebar-hover').prop('checked', false);
    $('body').removeClass('sidebar-hover');
    $('.logopanel2').remove();
    $.removeCookie('sidebar-hover');
}

/* Toggle Sidebar on Top */
function toggleSidebarHover() {
    if ($('body').hasClass('sidebar-hover')) removeSidebarHover();
    else createSidebarHover();
}

/* Create Sidebar Submenu visible on Hover */
function createSubmenuHover() {
    removeSidebarHover();
    handleSidebarFluid();
    $('#switch-submenu-hover').prop('checked', true);
    $('body').addClass('submenu-hover');
    $('.nav-sidebar .children').css('display', '');
    $.cookie('submenu-hover', 1);
    $('#switch-sidebar').prop('checked', false);
}

/* Remove Submenu on Hover */
function removeSubmenuHover() {
    $('#switch-submenu-hover').prop('checked', false);
    $('body').removeClass('submenu-hover');
    $('.nav-sidebar .nav-parent.active .children').css('display', 'block');
    $.removeCookie('submenu-hover');
}

/* Toggle Submenu on Hover */
function toggleSubmenuHover() {
    if ($('body').hasClass('submenu-hover')) removeSubmenuHover();
    else createSubmenuHover();
}

/* Create Topbar Fixed */
function handleTopbarFixed() {
    $('#switch-topbar').prop('checked', true);
    $('body').removeClass('fixed-topbar').addClass('fixed-topbar');
    $.removeCookie('fluid-topbar');
}

/* Create Topbar Fluid / Remove Topbar Fixed */
function handleTopbarFluid() {
    $('#switch-topbar').prop('checked', false);
    $('body').removeClass('fixed-topbar');
    if ($('body').hasClass('sidebar-top') && $('body').hasClass('fixed-sidebar')) {
        $('body').removeClass('fixed-sidebar');
        $('#switch-sidebar').prop('checked', false);
    }
    $.cookie('fluid-topbar', 1);
}

/* Toggle Topbar Fixed / Fluid */
function toggleTopbar() {
    if ($('body').hasClass('fixed-topbar')) handleTopbarFluid();
    else handleTopbarFixed();
}

/* Toggle Sidebar Collapsed */
function collapsedSidebar() {
    if ($body.css('position') != 'relative') {
        if (!$body.hasClass('sidebar-collapsed')) createCollapsedSidebar();
        else removeCollapsedSidebar();
    } else {
        if ($body.hasClass('sidebar-show')) $body.removeClass('sidebar-show');
        else $body.addClass('sidebar-show');
    }
}

function createCollapsedSidebar() {
    $body.addClass('sidebar-collapsed');
    $('.sidebar').css('width', '').resizable().resizable('destroy');
    $('.nav-sidebar ul').attr('style', '');
    $(this).addClass('menu-collapsed');
    destroySideScroll();
    $('#switch-sidebar').prop('checked');
    $.cookie('sidebar-collapsed', 1);
}

function removeCollapsedSidebar() {
    $body.removeClass('sidebar-collapsed');
    if (!$body.hasClass('submenu-hover')) $('.nav-sidebar li.active ul').css({
        display: 'block'
    });
    $(this).removeClass('menu-collapsed');
    if ($body.hasClass('sidebar-light') && !$body.hasClass('sidebar-fixed')) {
        $('.sidebar').height('');
    }
    createSideScroll();
    $.removeCookie('sidebar-collapsed');
}

/* Reset to Default Style, remove all cookie and custom layouts */
function resetStyle() {
    $('#reset-style').on('click', function (event) {
        event.preventDefault();
        removeSidebarHover();
        removeSidebarTop();
        removeSubmenuHover();
        removeCollapsedSidebar();
        $.removeCookie('main-color');
        $.removeCookie('main-name');
        $.removeCookie('theme');
        $.removeCookie('bg-name');
        $.removeCookie('bg-color');
        $.removeCookie('submenu-hover');
        $.removeCookie('sidebar-collapsed');
        $.removeCookie('sidebar-hover');
        $.removeCookie('main-color', { path: '/' });
        $.removeCookie('main-name', { path: '/' });
        $.removeCookie('theme', { path: '/' });
        $.removeCookie('bg-name', { path: '/' });
        $.removeCookie('bg-color', { path: '/' });
        $.removeCookie('sidebar-hover', { path: '/' });
        $.removeCookie('sidebar-top', { path: '/' });
        $('body').removeClass(function (index, css) {
            return (css.match(/(^|\s)bg-\S+/g) || []).join(' ');
        });
        $('body').removeClass(function (index, css) {
            return (css.match(/(^|\s)color-\S+/g) || []).join(' ');
        });
        $('body').removeClass(function (index, css) {
            return (css.match(/(^|\s)theme-\S+/g) || []).join(' ');
        });
        $('body').addClass('theme-sdtl').addClass('color-default');
        $('.builder .theme-color').removeClass('active');
        $('.theme-color').each(function () {
            if ($(this).data('color') == '#319DB5') $(this).addClass('active');
        });
        $('.builder .theme').removeClass('active');
        $('.builder .theme-default').addClass('active');
        $('.builder .sp-replacer').removeClass('active');
    });
}

/******************** END LAYOUT API  ************************/
/* ========================================================= */