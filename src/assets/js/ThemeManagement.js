$(document).ready(function () {
    $('#fe_themeSelector').on('change', function () {
        if ($('#fe_themeSelector').val()) {
            SendAjax($('#FeThemeManagement').attr('datatarget') + '/load/' + $(this).val(), [], "GET", function (data) {
                if (data.status == 'success') {
                    $('#FeThemeSettings').fadeOut(100, 'linear', function () {
                        $('#FeThemeSettings .panel-content').html(renderThemeSettings(data.settingList, data.siteDefaults));
                        handleiCheck();
                        $('#FeThemeSettings').fadeIn(200);
                    })
                }
            });
        }
    });

    $('#Theme_Management .btn_themesave').on('click', function () {
        var setting = {
            ThemeSelected: $('#fe_themeSelector').val(),
            themeSetting: {}
        };
        $.each($('#FeThemeSettings .form-control').serializeArray(), function (idx, elm) {
            if (Array.isArray(setting.themeSetting[elm['name']]) === true) {
                setting.themeSetting[elm['name']].push(elm['value']);
            } else if (setting.themeSetting[elm['name']] && setting.themeSetting[elm['name']].length > 0) {
                (setting.themeSetting[elm['name']] = [setting.themeSetting[elm['name']]]).push(elm['value']);
            } else {
                setting.themeSetting[elm['name']] = elm['value'];
            }
        });
        SendAjax($('#FeThemeManagement').attr('datatarget'), setting, "POST");
    });
});

function renderThemeSettings(settingList, defaults, heading) {
    heading = heading ? heading : 3;
    var html = '';
    $.each(settingList, function (key, settings) {
        heading = (heading > 5) ? 5 : heading;
        if (settings.type) {
            html += '<div class="ThemeSettings col-md-4 col-sm-12">' +
                '<div class="ThemeSettingHeading" >' +
                '<h6>' + key + '</h6>' +
                '</div>' +
                BuildFormControls(settings, defaults[settings.name]) +
                '</div > ';
        } else {
            html += '<div class="form-row"><h' + heading + '><strong>' + key + '</strong></h' + heading + '>' + renderThemeSettings(settings, defaults, heading + 1) + '</div>';

        }
    });
    return html;
}
