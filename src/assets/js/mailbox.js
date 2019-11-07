$(document).ready(function () {
    windowWidth = $(window).width();

    $('.go-back-list').on('click', function () {
        $('.email-details').fadeOut(200, function () {
            $('.emails-list').fadeIn();
        });
    });

    if (windowWidth < 800) {
        $('.emails-list .tab-content .message-item').on('click', function () {
            $('.emails-list').fadeOut(200, function () {
                $('.email-details').fadeIn();
                customScroll();
            });
        });
    }

    $(document).on('click','.email-details .btnRemoveNotification',function(e){
        removeNotification($(this).attr('tartget'));
    });

    /* Display selected email */
    $('.emails-list').on('click', '.message-item', function (e) {
        loadNotification($(this).attr('mail_id'));
    });

    /*  Search Function  */
    if ($('input#email-search').length) {
        $('input#email-search').val('').quicksearch('.active .message-item', {
            selector: '.subject-text',
            'onAfter': function () {
                customScroll();
            },
        });
    }

    $(window).resize(function () {
        windowWidth = $(window).width();
        if (windowWidth > 800) {
            $('.emails-list, .email-details').css('display', 'table-cell');
        }
        else {
            $('.email-details').css('display', 'none');
            $('.emails-list .tab-content .message-item').on('click', function () {
                $('.emails-list').fadeOut(200, function () {
                    $('.email-details').fadeIn();
                    customScroll();
                });
            });
        }


    });

    /* Context Menu */
    var emailMenuContext = '<div id="context-menu" class="email-context dropdown clearfix">' +
        '<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">' +
        '<li class="border-top"><a data-remove="true" href="#"><i class="fa fa-times"></i> Remove Email</a></li>' +
        '</ul>' +
        '</div>';
    $('.main-content').append(emailMenuContext);
    var $contextMenu = $("#context-menu");
    $('.emails-list').on('mousedown', '.message-item', function () {
        $(this).contextmenu({
            target: '#context-menu',
            onItem: function (context, e) {
                if ($(e.target).data("remove")) {
                    removeNotification($(context).attr('mail_id'));
                }
            }
        });
    });
});

function loadNotification(MID){
    $('.email-details .btnRemoveNotification').attr('tartget', MID);
    SendAjax($('#fe_notification').attr('MailTargt') + '/' + MID, [], 'POST', function (data) {
        toggleDetailWin(true);
        if (undefined != data.id) {
            $('.email-details h1').fadeOut(200, function () {
                $(this).text(data.subject).fadeIn(200);
            });
            $('.email-details .sender').fadeOut(200, function () {
                $(this).text((data.sender == undefined ? 'System' : data.sender.name)).fadeIn(200);
            });
            $('.email-details .date').fadeOut(200, function () {
                $(this).text(data.send_time).fadeIn(200);
            });
            $('.email-details .email-content').fadeOut(200, function () {
                $(this).html(data.contents).fadeIn(200);
                customScroll();
            });
        }
    }, true);
}

function toggleDetailWin(show_status){
    ToShow = ((undefined == show_status) ? !$('.email-details').is(":visible") : show_status);
    if (ToShow){
        $('.email-details').fadeIn(200);
    }else{
        $('.email-details').fadeOut(200);
    }
}

function removeNotification(MID){
    SendAjax($('#fe_notification').attr('MailTargt') + '/remove/' + MID, [], 'POST', function (data) {
        if (data.status=='success'){
            var tar = $('.message-item[mail_id="' + MID + '"]');
            tar.slideUp(200, function () {
                tar.remove();
                if ($('.message-item:first').length > 0 && $('.email-details').is(":visible")) {
                    loadNotification($($('.message-item:first')[0]).attr('mail_id'));
                } else {
                    toggleDetailWin(false);
                }
            });
        }
    });
}
