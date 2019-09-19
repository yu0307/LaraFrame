var RoamingWidget = {};
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

    $('#new_widget_area #widget_add').on('click', function () {
        // $('#fe_widgetArea #shadow_widget').toggleClass('fadeOutUp');
        clearWidgetWin();
        if ($('#fe_widget_list').find('i.loading').length > 0) {
            loadWidgetList();
        }
        $('#dashboardWidgetControl').modal('show');
    });

    $('#widget_add').on('click', function () {
        if ($('#site_widgets').val().length > 0) {
            add_widget($('#site_widgets').val());
            clearWidgetWin();
            $('#dashboardWidgetControl').modal('hide');
        } else {
            Notify('Please select a widget from the dropdown.', 'error');
        }
    });

    $(document).on('WidgetLayoutChanged', function () {
        updateLayout();
    });
});

function loadWidgetDetails(WidgetName) {
    $('#fe_widget_desc').html('<div class="text-center sm-col-12 m-t-10"><i class= "fa fa-spinner fa-spin fa-3x fa-fw loading" ></i><div class="text-center ">Loading Widget Details...</div></div>');
    SendAjax('/GetWidgetDetails/' + WidgetName, [], 'GET', function (data, status) {
        if (data !== undefined && $.isEmptyObject(data) === false) {
            RoamingWidget = data;
            $('#fe_widget_desc').html(data.Description);
        } else {
            $('#fe_widget_desc').html('<div class="text-center"><h3>Widget Info Unavailable...</h3></div>');
        }
    });
}

function loadWidgetList() {
    SendAjax('/GetWidgetList', [], 'GET', function (data, status) {
        var new_WidgetList = '<div class="text-center">Site Widetes not available...</div>';
        if (data !== undefined && $.isEmptyObject(data) === false) {
            new_WidgetList = '<h3><strong>Select</strong> your widget from the list below</h3><select class="btn-block" name="site_widgets" id="site_widgets" style="width:100%"><option value="">Select A Widget</option>';
            $.each(data, function (widget, settings) {
                new_WidgetList += '<option value="' + widget + '">' + widget + '</option>';
            });
            new_WidgetList += '</select>';
        }
        $('#fe_widget_list').fadeOut(1000, 'linear', function () {
            $('#fe_widget_list').html(new_WidgetList).fadeIn(1000).find('select').on('change', function () {
                if ($(this).val().length > 0) loadWidgetDetails($(this).val());
            }).select2();
        })
    });
}

function removeWidgetPanel(tar) {
    if ($(tar).parent('.fe_widget:first').attr('id').length > 0) {
        removeAjaxWidget($(tar).parent('.fe_widget:first').attr('id'));
    }
    $(tar).parent('.fe_widget:first').remove();
    $(document).trigger('WidgetLayoutChanged');
}

function clearWidgetWin() {
    $('#fe_widget_desc').html('');
    $('#site_widgets').val(null).trigger('change');
    RoamingWidget = {};
}

function add_widget(widget) {
    if (widget !== undefined && widget.length > 0) {
        SendAjax('/GetWidget/' + widget, [], 'POST', function (data, status) {
            var new_Widget = data;
            var WidgetSetting = new_Widget.settings;
            if ($('.fe_widget:last').length <= 0) {
                $(initNewWidget(new_Widget.html, WidgetSetting)).appendTo($('#fe_widgetCtrls'));
            } else {
                $(initNewWidget(new_Widget.html, WidgetSetting)).insertAfter($('.fe_widget:last'));
            }
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
