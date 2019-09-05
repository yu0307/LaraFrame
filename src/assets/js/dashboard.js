$(document).ready(function () {
    Sortable.create(fe_widgetCtrls, {
        animation: 150,
        sort: true,
        filter: ".feWidgetFixed",  // Selectors that do not lead to dragging (String or Function)
        draggable: ".fe_widget",  // Specifies which items inside the element should be draggable
        easing: "cubic-bezier(0.075, 0.82, 0.165, 1)",
        dragClass: "sortable_drag",  // Class name for the dragging item
        ghostClass: 'sortGhosting',
        onSort: function (e) {
            $(document).trigger('WidgetLayoutChanged');
        }
    });

    $('#fe_widgetArea .panel').on('PanelRemoved', function (e) {
        removeWidgetPanel($(this));
    });

    $('#site_widgets').select2();

    $('#new_widget_area #widget_add').on('click', function () {
        $('#fe_widgetArea #shadow_widget').toggleClass('fadeOutUp');

    });

    $('#shadow_widget .btn-cancel').on('click', function () {
        clearWidgetWin();
    });

    $('#shadow_widget .btn-save').on('click', function () {
        if ($('#site_widgets').val().length > 0) {
            add_widget($('#site_widgets').val());
            clearWidgetWin();
        } else {
            Notify('Please select a widget from the dropdown.', 'error');
        }
    });

    $('#site_widgets').on('change', function () {
        $('#shadow_widget .widget_description').text(SiteWidgets[$(this).val()]);
    });

    $(document).on('WidgetLayoutChanged', function () {
        updateLayout();
    });
});

function removeWidgetPanel(tar) {
    if ($(tar).parent('.fe_widget:first').attr('id').length > 0) {
        removeAjaxWidget($(tar).parent('.fe_widget:first').attr('id'));
    }
    $(tar).parent('.fe_widget:first').remove();
    $(document).trigger('WidgetLayoutChanged');
}

function clearWidgetWin() {
    $('#fe_widgetArea #shadow_widget').toggleClass('fadeOutUp');
    $('#site_widgets').val(null).trigger('change');
    $('#shadow_widget .widget_description').text('');
}

function add_widget(widget) {
    if (widget !== undefined && widget.length > 0) {
        SendAjax('/GetWidget/' + widget, [], 'POST', function (data, status) {
            var new_Widget = data;
            var WidgetSetting = new_Widget.settings;
            $(initNewWidget(new_Widget.html, WidgetSetting)).insertAfter($('.fe_widget:not("#shadow_widget"):last'));
            if (WidgetSetting.AjaxLoad === true) {
                if (undefined === window.AjaxWidgetPool) {//load ajax script if not exist
                    $.getScript("/feiron/felaraframe/widgets/WidgetAjax.js")
                        .done(function (script, textStatus) {
                            AjaxWidgetPool[WidgetSetting.ID] = WidgetSetting.Ajax;
                        });
                } else {
                    AjaxWidgetPool[WidgetSetting.ID] = WidgetSetting.Ajax;
                }
                checkAjaxStatus(WidgetSetting.ID, WidgetSetting.Ajax);
                if (undefined !== WidgetSetting.Ajax.AjaxJS) {
                    $.getScript(WidgetSetting.Ajax.AjaxJS);
                }
            }
            $(document).trigger('WidgetLayoutChanged');
        });
    }
}

function initNewWidget(widget, WidgetSetting) {
    widget = $(widget);
    $(widget).find('.withScroll').css('height', (WidgetSetting.DataHeight + 'px')).mCustomScrollbar();
    $(widget).find('.panel-controls').each(function () {
        var controls_html = '<div class="control-btn">' + '<a href="#" class="panel-reload hidden"><i class="icon-reload"></i></a>' + '<a href="#" class="panel-popout hidden tt" title="Pop Out/In"><i class="icons-office-58"></i></a>' + '<a href="#" class="panel-maximize hidden"><i class="icon-size-fullscreen"></i></a>' + '<a href="#" class="panel-toggle"><i class="fa fa-angle-down"></i></a>' + '<a href="#" class="panel-close"><i class="icon-trash"></i></a>' + '</div>';
        $(this).append(controls_html).find('.panel-close').on("click", function () {
            remove_panel($(this));
        });
    });
    $(widget).find('.panel').on('PanelRemoved', function (e) {
        removeWidgetPanel($(this));
    });
    if (WidgetSetting.AjaxLoad === true) {
        bindWidgetReloadEvent($(widget).find('.panel-header .panel-reload'));
        bindWidgetReloadResponse($(widget));
    }
    return widget;
}

function updateLayout() {
    var layout = [];
    $('#fe_widgetCtrls .fe_widget:not(.pendingRemoval)').each(function () {
        if ($(this).attr('name') !== undefined && $(this).attr('name').length > 0) {
            layout.push($(this).attr('name'));
        }
    });
    $.ajax({
        type: 'POST',
        url: '/updateWidgetLayout',
        dataType: 'json',
        data: { layout },
        complete: function (data, textStatus, jqXHR) {
            console.log(data.responseJSON);
        }
    });
}
