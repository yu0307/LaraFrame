var WidgetPool = {};
var GlobalWidgetTimer;
$(document).ready(function () {
    var globalWidgetList = {};
    $.each(WidgetPool, function (key, widget) {
        widget.AjaxInterval = (undefined == widget.AjaxInterval) ? false : widget.AjaxInterval;
        $('#' + key).find('.control-btn .panel-reload').addClass('visible');
        loadWidgetAjax(key, widget);
        switch (widget.AjaxInterval) {
            case true://load with global timer
                globalWidgetList[key] = (widget);
                break;
            case false://load once
                break;

            default://load with specific timer
                setInterval(function () { loadWidgetAjax(key, widget) }, widget.AjaxInterval);
                break;
        }
    });
    if (!$.isEmptyObject(globalWidgetList)) {
        GlobalWidgetTimer = setInterval(function () {
            $.each(globalWidgetList, function (key, widget) {
                loadWidgetAjax(key, widget);
            });
        }, 15000);
    }
    $(document).off("click", '.panel-header .panel-reload');
    // Reload Panel Content
    $(document).on("click", '.widgetArea .panel-header .panel-reload', function (event) {
        event.preventDefault();
        $(this).parents(".fe_widget:first").trigger('ReloadWidget');
        event.stopPropagation();
    });

    $(".fe_widget .panel").on("PanelRemoved", function (event) {
        $(this).parents('.fe_widget:first').remove();
    });

    $('.fe_widget').on('ReloadWidget', function (event) {
        loadWidgetAjax($(this).attr('id'), WidgetPool[$(this).attr('id')]);
        event.stopPropagation();
    });
});

function triggerUpdate(target) {
    $(target).trigger('AjaxUpdated');
}

function loadWidgetAjax(widgetType, widget) {
    var el = $('#' + widgetType).find(".panel:first");
    blockUI(el);
    $.ajax({
        type: widget.AjaxType,
        url: widget.AjaxURL,
        dataType: 'json',
        success: function (data, textStatus, jqXHR) {
            $('#' + data.target).trigger('AjaxUpdated', data);
        },
        complete: function () {
            unblockUI(el);
        },
        error: function (jqXHR, textStatus) {
            Notify('Error getting widget data [' + widgetType + ':' + widget.ID + ']', 'error');
        }
    });
}

