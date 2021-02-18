var RoamingWidget = {};
var LoadedDynamicResources = [];
var DashBoardWidgetBank = [];
$(document).ready(function () {
    Sortable.create(fe_widgetCtrls, {
        animation: 150,
        sort: true,
        filter: ".feWidgetFixed,.panel-content",  // Selectors that do not lead to dragging (String or Function)
        draggable: ".fe_widget",  // Specifies which items inside the element should be draggable
        handle: ".my-handle, .panel-controls",  // Drag handle selector within list items
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
            userSettings = {};
            $.each($('#fe_widget_desc .form-control').serializeArray(), function (key, em) {
                userSettings[em.name] = em.value;
            });

            add_widget($('#site_widgets').val(), userSettings);
            clearWidgetWin();
            $('#dashboardWidgetControl').modal('hide');
        } else {
            Notify('Please select a widget from the dropdown.', 'error');
        }
    });

    $(document).on('WidgetLayoutChanged', function () {
        updateLayout();
    });

    $('.HasSettingOutlet').each(function () {
        $('<a class="panel-setting"><i class="icon-settings"></i></a>').insertBefore($(this).find('.panel-close').first());
    });

    $(document).on("click", '.panel-header .panel-setting', function (event) {
        event.preventDefault();
        ToggleWidgetUsrSetting($(this), true);
    });
    $(document).on("click", '.fe_widget .back .hideUsrSetting', function (event) {
        event.preventDefault();
        ToggleWidgetUsrSetting($(this), false);
    });
    $(document).on("click", '.fe_widget .back .saveUsrSetting', function (event) {
        event.preventDefault();
        SyncWidgetSetting($(this).parents('.fe_widget:first'));
    });
});

function SyncWidgetSetting(tar) {
    userSettings = {};
    $.each($(tar).find('.form-control').serializeArray(), function (key, em) {
        userSettings[em.name] = em.value;
    });
    updateWidgetSetting($(tar).attr('usrkey'), userSettings);
}

function ToggleWidgetUsrSetting(tar, status) {
    var usrkey = $(tar).parents('.fe_widget:first').attr('usrkey');
    var btn = $(tar).parents('.fe_widget:first').find('.icon-settings:first');
    if (DashBoardWidgetBank['wg_' + usrkey] !== undefined) {
        tar = $(tar).parents('.fe_widget:first').find('.panel-content:first');
        if ($(tar).find('.back').length <= 0) {
            $(tar).append('<div class="back p-10" style="display:none;">' + InitWidgetUsrSetting(DashBoardWidgetBank['wg_' + usrkey].settings) + '<div class="pull-left btn btn-success saveUsrSetting">Update</div> <div class="pull-right btn btn-danger hideUsrSetting">Cancel</div> </div>');
            $(tar).flip({
                trigger: 'manual',
                speed: 300,
                front: $(tar).find('.wg_main_cnt:first')
            }, function () {
                $(tar).flip(status, function () {
                    if (status) {
                        $(tar).find('.back').fadeIn(200); $(btn).fadeOut(100);
                    } else {
                        $(tar).find('.back').fadeOut(200); $(btn).fadeIn(100);
                    }

                });
            });
        } else {
            $(tar).flip(status, function () {
                if (status) {
                    $(tar).find('.back').fadeIn(200); $(btn).fadeOut(100);
                } else {
                    $(tar).find('.back').fadeOut(200); $(btn).fadeIn(100);
                }

            });
        }

    }
}

function loadWidgetDetails(WidgetName) {
    $('#fe_widget_desc').html('<div class="text-center sm-col-12 m-t-10"><i class= "fa fa-spinner fa-spin fa-3x fa-fw loading" ></i><div class="text-center ">Loading Widget Details...</div></div>');
    SendAjax('/GetWidgetDetails/' + WidgetName, [], 'GET', function (data, status) {
        $('#fe_widget_desc').fadeOut(200, 'linear', function () {
            if (data !== undefined && $.isEmptyObject(data) === false) {
                RoamingWidget = data;
                data.Description = '<div class="w-100 p-10">' + data.Description + '<hr class="m-0 m-t-5"/></div>';
                if (data.userSettingOutlet !== undefined && $.isEmptyObject(data.userSettingOutlet) === false) {
                    data.Description += InitWidgetUsrSetting(data.userSettingOutlet);
                }
                $('#fe_widget_desc').html(data.Description).find('select').select2();
            } else {
                $('#fe_widget_desc').html('<div class="text-center"><h3>Widget Info Unavailable...</h3></div>');
            }
            $('#fe_widget_desc').fadeIn();
        });
    });
}

function InitWidgetUsrSetting(usrSetting) {
    var settingHtml = '<h3 class="m-t-10 w-100">Widget Settings:</h3>';
    $(usrSetting).each(function (idx, setting) {
        settingHtml += ' <div class="form-group row">' +
            '<label class="col-sm-12 col-md-3 control-label">' + setting.key + '</label>' +
            '<div class="col-md-9 col-sm-12" >';
        switch (setting.type) {
            case 'text':
                settingHtml += '<div class="input-group" > ' +
                    '<span class="input-group-addon"><i class="fa fa-gear"></i></span > ' +
                    '<input name="' + setting.key + '" value="' + (setting.value === undefined ? '' : setting.value) + '" type ="text" class="form-control form-white" placeholder = "' + setting.placeholder + '" > ' +
                    '</div > ';
                break;
            case 'radio':
                if (setting.options !== undefined && $.isEmptyObject(setting.options) === false) {
                    settingHtml += '<div class="icheck-inline">';
                    $(setting.options).each(function (idx, option) {
                        settingHtml += '<label><input class="form-control" type="radio" ' + (option == setting.value ? 'CHECKED' : '') + ' name="' + setting.key + '" data-radio="iradio_minimal-blue" value="' + option + '">' + option + '</label>';
                    });
                    settingHtml += '</div>';
                }
                break;
            case 'select':
                if (setting.options !== undefined && $.isEmptyObject(setting.options) === false) {
                    settingHtml += '<select name="' + setting.key + '" class="form-control">';
                    $(setting.options).each(function (idx, option) {
                        settingHtml += '<option value="' + option + '">' + option + '</option>';
                    });
                    settingHtml += '</select>';
                }
                break;
        }

        settingHtml += '</div > ' +
            '</div > ';
    });
    return '<div class="panel">' + settingHtml + '</div>';
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
        $('#fe_widget_list').fadeOut(300, 'linear', function () {
            $('#fe_widget_list').html(new_WidgetList).fadeIn(300).find('select').on('change', function () {
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

function add_widget(widget, settings) {
    settings = settings || [];
    if (widget !== undefined && widget.length > 0) {
        SendAjax('/GetWidget/' + widget, { 'userSetting': settings }, 'POST', function (data, status) {
            var new_Widget = data;
            var WidgetSetting = new_Widget.settings;
            $(initNewWidget(new_Widget.html, WidgetSetting)).appendTo($('#fe_widgetCtrls'));
            $(WidgetSetting.scripts).each(function (indx, elm) {
                loadWidgetResource(elm);
            });
            delete WidgetSetting.scripts;
            $(WidgetSetting.styles).each(function (indx, elm) {
                loadWidgetResource(elm);
            });
            delete WidgetSetting.styles;
            var usrSetting = (undefined == WidgetSetting.usrSettings) ? [] : WidgetSetting.usrSettings;;
            delete WidgetSetting.usrSettings;
            DashBoardWidgetBank['wg_' + WidgetSetting.ID] = { settings: usrSetting, widgetConfig: WidgetSetting };
            $('#' + WidgetSetting.ID).trigger('wg_added', { 'Setting': WidgetSetting });
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

            // $(document).trigger('WidgetLayoutChanged');
        });
    }
}

function updateUserWidgetSetting(ID, Setting) {
    WidgetUserSettings[ID] = Setting;
}

function initNewWidget(widget, WidgetSetting) {
    widget = $(widget);
    $(widget).find('.withScroll').css('height', (WidgetSetting.DataHeight + 'px')).mCustomScrollbar();
    $(widget).find('.panel-controls').each(function () {
        var controls_html = '<div class="control-btn">' +
            '<a href="#" class="panel-reload hidden"><i class="icon-reload"></i></a>' +
            '<a href="#" class="panel-popout hidden tt" title="Pop Out/In"><i class="icons-office-58"></i></a>' +
            '<a href="#" class="panel-maximize hidden"><i class="icon-size-fullscreen"></i></a>' +
            '<a href="#" class="panel-toggle"><i class="fa fa-angle-down"></i></a>' +
            ((WidgetSetting.usrSettings !== undefined && !$.isEmptyObject(WidgetSetting.usrSettings)) ? '<a class="panel-setting"><i class="icon-settings"></i></a>' : '') +
            '<a href="#" class="panel-close"><i class="icon-trash"></i></a>' +
            '</div>';
        $(this).append(controls_html).find('.panel-close').on("click", function () {
            remove_panel($(this));
        });
        if (WidgetSetting.usrSettings !== undefined && !$.isEmptyObject(WidgetSetting.usrSettings)) {
            DashBoardWidgetBank['wg_' + WidgetSetting.ID] = { settings: WidgetSetting.usrSettings };
        }
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

function updateWidgetSetting(tar, setting) {
    $.ajax({
        type: 'POST',
        url: '/updateUserWidgetSetting',
        dataType: 'json',
        data: { target: tar, Settings: setting },
        complete: function (data, textStatus, jqXHR) {
            if (data.responseJSON.status == 'success') {
                Notify('Widget Setting Updated.');
                $('#' + tar).trigger('wgUserSettingUpdated', { 'Setting': setting, 'ID': tar });
                ToggleWidgetUsrSetting($('#' + tar).find('.panel-content:first()'), false);
            }
        }
    });
}

function updateLayout() {
    var layout = [];
    $('#fe_widgetCtrls .fe_widget:not(.pendingRemoval)').each(function () {
        if ($(this).attr('usrkey') !== undefined && $(this).attr('usrkey').length > 0) {
            layout.push($(this).attr('usrkey'));
        }
    });
    $.ajax({
        type: 'POST',
        url: '/updateWidgetLayout',
        dataType: 'json',
        data: { newLayout: layout },
        complete: function (data, textStatus, jqXHR) {
            console.log(data.responseJSON);
        }
    });
}

function loadWidgetResource(resource) {
    if (resource !== undefined) {
        var extension = resource.file.substr((resource.file.lastIndexOf('.') + 1));
        if (extension == 'css') {
            if ($('link[href$="' + resource.file + '"]').length <= 0) {
                $('head').append($('<link rel="stylesheet" type="text/css" media="screen" />').attr('href', resource.file));
            }
        } else {
            if (resource.duplicate != undefined && resource.duplicate !== true && $('script[src$="' + resource.file + '"]').length <= 0 && false === LoadedDynamicResources.includes(resource.file)) {
                $.getScript(resource.file);
                LoadedDynamicResources.push(resource.file);
            }
        }
    }
}

