var AjaxWidgetPool = {};
var GlobalWidgetTimer;
var WidgetTimer = {};
var globalWidgetList;
$(document).ready(function () {
    globalWidgetList = {};
    $.each(AjaxWidgetPool, function (key, widget) {
        checkAjaxStatus(key, widget);
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
    bindWidgetReloadEvent($('.widgetArea .panel-header .panel-reload'));
    bindWidgetReloadResponse($('.fe_widget'));

});

function bindWidgetReloadEvent(tar) {
    $(tar).on("click", function (event) {
        event.preventDefault();
        $(this).parents(".fe_widget:first").trigger('ReloadWidget');
        event.stopPropagation();
    });
}

function bindWidgetReloadResponse(tar) {
    $(tar).on('ReloadWidget', function (event) {
        loadWidgetAjax($(this).attr('id'), AjaxWidgetPool[$(this).attr('id')]);
        event.stopPropagation();
    });
}

function checkAjaxStatus(key, widget) {
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
            WidgetTimer[key] = setInterval(function () { loadWidgetAjax(key, widget) }, widget.AjaxInterval);
            break;
    }
}

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

function removeAjaxWidget(key) {
    if (undefined !== WidgetTimer[key]) {
        clearInterval(WidgetTimer[key]);
        delete WidgetTimer[key];
    }
    if (undefined !== globalWidgetList[key]) {
        delete globalWidgetList[key];
    }
}
