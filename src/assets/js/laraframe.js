function toggleQuickview(){
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

$(document).ready(function(){
    $('#quickview-sidebar').on('sidebarHidden', function () {
        //if Notes control is used. 
        if ($('#notes').length) {
            $('.list-notes').addClass('current');
            $('.detail-note').removeClass('current');
        }
    });
    $("#quickview-sidebar form").submit(function (e) {
        var tar_form=$(this);
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: $(this).prop('action'),
            dataType: 'json',
            data: $(this).serialize(),
            complete: function (jqXHR, status) {
                var data = jqXHR.responseJSON;
                message = jQuery.map(data.message, function (n, i) {
                    return ('<div><h5>'+i+'</h5>'+n+'</div>');
                }).join("");
                Notify(message, data.status);
                tar_form.trigger('quickview_form_submited');
            }
        });
    });
});

function Notify(content, type = 'success', position = 'topCenter', container = '', confirm = false, method = 3000) {

    if (position == 'bottom') {
        openAnimation = 'animated fadeInUp';
        closeAnimation = 'animated fadeOutDown';
    }
    else if (position == 'top') {
        openAnimation = 'animated fadeIn';
        closeAnimation = 'animated fadeOut';
    }
    else {
        openAnimation = 'animated bounceIn';
        closeAnimation = 'animated bounceOut';
    }

    if (container == '') {
        content = '<div class="alert alert-' + type + ' media fade in">' + content +'</div>';
        var n = noty({
            text: content,
            type: type,
            dismissQueue: true,
            layout: position,
            closeWith: ['click'],
            theme: "made",
            maxVisible: 10,
            animation: {
                open: openAnimation,
                close: closeAnimation,
                easing: 'swing',
                speed: 70
            },
            timeout: method,
            buttons: confirm ? [
                {
                    addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
                        $noty.close();
                        noty({
                            dismissQueue: true, layout: 'topRight', theme: 'defaultTheme', text: 'You clicked "Ok" button', animation: {
                                open: 'animated bounceIn', close: 'animated bounceOut'
                            }, type: 'success', timeout: 3000
                        });
                        confirm = false;
                    }
                },
                {
                    addClass: 'btn btn-danger', text: 'Cancel', onClick: function ($noty) {
                        $noty.close();
                        noty({
                            dismissQueue: true, layout: 'topRight', theme: 'defaultTheme', text: 'You clicked "Cancel" button', animation: {
                                open: 'animated bounceIn', close: 'animated bounceOut'
                            }, type: 'error', timeout: 3000
                        });
                        confirm = false;
                    }
                }
            ] : '',
            callback: {
                onShow: function () {
                    leftNotfication = $('.sidebar').width();
                    if ($('body').hasClass('rtl')) {
                        if (position == 'top' || position == 'bottom') {
                            $('#noty_top_layout_container').css('margin-right', leftNotfication).css('left', 0);
                            $('#noty_bottom_layout_containe').css('margin-right', leftNotfication).css('left', 0);
                        }
                        if (position == 'topRight' || position == 'centerRight' || position == 'bottomRight') {
                            $('#noty_centerRight_layout_container').css('right', leftNotfication + 20);
                            $('#noty_topRight_layout_container').css('right', leftNotfication + 20);
                            $('#noty_bottomRight_layout_container').css('right', leftNotfication + 20);
                        }
                    }
                    else {
                        if (position == 'top' || position == 'bottom') {
                            $('#noty_top_layout_container').css('margin-left', leftNotfication).css('right', 0);
                            $('#noty_bottom_layout_container').css('margin-left', leftNotfication).css('right', 0);
                        }
                        if (position == 'topLeft' || position == 'centerLeft' || position == 'bottomLeft') {
                            $('#noty_centerLeft_layout_container').css('left', leftNotfication + 20);
                            $('#noty_topLeft_layout_container').css('left', leftNotfication + 20);
                            $('#noty_bottomLeft_layout_container').css('left', leftNotfication + 20);
                        }
                    }

                }
            }
        });

    }
    else {
        var n = $(container).noty({
            text: content,
            dismissQueue: true,
            layout: position,
            closeWith: ['click'],
            theme: "made",
            maxVisible: 10,
            buttons: confirm ? [
                {
                    addClass: 'btn btn-primary', text: 'Ok', onClick: function ($noty) {
                        $noty.close();
                        noty({
                            dismissQueue: true, layout: 'topRight', theme: 'defaultTheme', text: 'You clicked "Ok" button', animation: {
                                open: 'animated bounceIn', close: 'animated bounceOut'
                            }, type: 'success', timeout: 3000
                        });
                    }
                },
                {
                    addClass: 'btn btn-danger', text: 'Cancel', onClick: function ($noty) {
                        $noty.close();
                        noty({
                            dismissQueue: true, layout: 'topRight', theme: 'defaultTheme', text: 'You clicked "Cancel" button', animation: {
                                open: 'animated bounceIn', close: 'animated bounceOut'
                            }, type: 'error', timeout: 3000
                        });
                    }
                }
            ] : '',
            animation: {
                open: openAnimation,
                close: closeAnimation
            },
            timeout: method,
            callback: {
                onShow: function () {
                    var sidebarWidth = $('.sidebar').width();
                    var topbarHeight = $('.topbar').height();
                    if (position == 'top' && style == 'topbar') {
                        $('.noty_inline_layout_container').css('top', 0);
                        if ($('body').hasClass('rtl')) {
                            $('.noty_inline_layout_container').css('right', 0);
                        }
                        else {
                            $('.noty_inline_layout_container').css('left', 0);
                        }

                    }

                }
            }
        });

    }

}