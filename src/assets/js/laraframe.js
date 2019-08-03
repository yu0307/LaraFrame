$(document).ready(function(){
    Noty.overrideDefaults({
        layout: 'topCenter',
        type:'success',
        theme: 'bootstrap-v3',
        dismissQueue: true,
        closeWith: ['click'],
        maxVisible: 10,
        timeout:3000,
        progressBar:true,
        animation: {
            open: 'animated bounceIn',
            close: 'animated bounceOut',
            easing: 'swing',
            speed: 70
        }
    });

    $('#quickview-sidebar').on('sidebarHidden', function () {
        //if Notes control is used. 
        if ($('#notes').length) {
            $('.list-notes').addClass('current');
            $('.detail-note').removeClass('current');
        }
    });
    $("#quickview-sidebar form").submit(function (e) {
        var tar_form=$(this);
        var method = tar_form.find('input[name="method"]').val();
        e.preventDefault();
        SendAjax(
            tar_form.prop('action'), 
            tar_form.serialize(), 
            method, 
            function (jqXHR, status) {
                var data = jqXHR.responseJSON;
                message = jQuery.map(data.message, function (n, i) {
                    return ('<div>' + n + '</div>');
                }).join("");
                Notify(message, data.status);
                tar_form.trigger('quickview_form_submited');
                if (tar_form.attr('form_type').length>0){
                    tar_form.trigger((tar_form.attr('form_type') +'_'+ method));
                }
            });
        tar_form.find('input[name="method"]').val('POST');
    });
});

function SendAjax(URL,DATA,TYPE='POST',callback=null){
    $.ajax({
        type: TYPE,
        url: URL,
        dataType: 'json',
        data: DATA,
        complete: function (jqXHR, status) {
            if (typeof callback === 'function'){
                callback(jqXHR, status);
            }
        }
    });
}

function toggleQuickview() {
    if ($('#quickview-sidebar').hasClass('open')) {
        $('#quickview-sidebar').addClass('closing');
        $('#quickview-sidebar').removeClass('open');
        setTimeout(function () {
            $('#quickview-sidebar').removeClass('closing').trigger('sidebarHidden');
        }, 400);
    }
    else
        $('#quickview-sidebar').addClass('open').trigger('sidebarShown');
}

function Notify(content, type = 'success', position = 'topCenter', container = '', confirm = false, method = 3000) {
    if (position == 'bottom') {
        openAnimation = 'animated fadeInUp';
        closeAnimation = 'animated fadeOutDown';
    }
    else if (position == 'top' || container.length>1) {
        openAnimation = 'animated fadeIn';
        closeAnimation = 'animated fadeOut';
    }
    else {
        openAnimation = 'animated bounceIn';
        closeAnimation = 'animated bounceOut';
    }
    new Noty({
        text: content,
        type: type,
        layout: position,
        timeout: method,
        container: container,
        animation: {
            open: openAnimation,
            close: closeAnimation
        },
        buttons: confirm ? [
            Noty.button('Ok', 'btn btn-primary', function ($noty) {
                $noty.close();
                confirm = false;
            }, { id: 'button1', 'data-status': 'ok' })
        ] : '',
        callback: {
            onShow: function () {
                if (container==''){
                    leftNotfication = $('.sidebar').width();
                    if ($('body').hasClass('rtl')) {
                        if (position == 'top' || position == 'bottom') {
                            $('#noty_layout_top').css('margin-right', leftNotfication).css('left', 0);
                            $('#noty_layout_bottom').css('margin-right', leftNotfication).css('left', 0);
                        }
                        if (position == 'topRight' || position == 'centerRight' || position == 'bottomRight') {
                            $('#noty_layout_centerRight').css('right', leftNotfication + 20);
                            $('#noty_layout_topRight').css('right', leftNotfication + 20);
                            $('#noty_layout_bottomRight').css('right', leftNotfication + 20);
                        }
                    }
                    else {
                        if (position == 'top' || position == 'bottom') {
                            $('#noty_layout_top').css('margin-left', leftNotfication).css('right', 0);
                            $('#noty_layout_bottom').css('margin-left', leftNotfication).css('right', 0);
                        }
                        if (position == 'topLeft' || position == 'centerLeft' || position == 'bottomLeft') {
                            $('#noty_layout_centerLeft').css('left', leftNotfication + 20);
                            $('#noty_layout_topLeft').css('left', leftNotfication + 20);
                            $('#noty_layout_bottomLeft').css('left', leftNotfication + 20);
                        }
                    }
                }
            }
        }
    }).show();
}