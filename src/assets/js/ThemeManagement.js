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
    
    livePreview();
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
            html += '<div class="form-row row"><h' + heading + '><strong>' + key + '</strong></h' + heading + '>' + renderThemeSettings(settings, defaults, heading + 1) + '</div>';

        }
    });
    return html;
}

// FeLaraFrameSpecific
function livePreview() {
    $('input:radio[name="sb_structure"]').on('ifChecked', function () {
        if ($(this).val() == 'Condensed') {
            $('body').addClass('sidebar-condensed');
        } else {
            $('body').removeClass('sidebar-condensed');
        }
    });
    $('input:radio[name="sb_style"]').on('ifChecked', function () {
        if ($(this).val() == 'Fixed') {
            handleSidebarFixed();
        } else {
            removeSidebarHover();
            $('input:radio[name="sb_showon"][value="Always"]').iCheck('check');
            handleSidebarFluid();
        }
    });
    $('input:radio[name="sb_showon"]').on('ifChecked', function () {
        if ($(this).val() == 'Hover') {
            createSidebarHover();
            $('input:radio[name="sb_style"][value="Fixed"]').iCheck('check');
        } else {
            removeSidebarHover();
        }
    });
    $('input:radio[name="sb_subshowon"]').on('ifChecked', function () {
        if ($(this).val() == 'Hover') {
            createSubmenuHover();
        } else {
            removeSubmenuHover();
        }
    });
    $('input:radio[name="sb_initbh"]').on('ifChecked', function () {
        if ($(this).val() == 'Normal') {
            removeCollapsedSidebar();
        } else {
            createCollapsedSidebar();
        }
    });
    $('input:radio[name="tb_location"]').on('ifChecked', function () {
        if ($(this).val() == 'Fixed') {
            handleTopbarFixed();
        } else {
            handleTopbarFluid();
        }
    });
    $('input:radio[name="page_display"]').on('ifChecked', function () {
        if ($(this).val() == 'Boxed') {
            createBoxedLayout();
        } else {
            removeBoxedLayout();
        }
    });
    $('input:radio[name="page_template"]').on('ifChecked', function () {
        switch ($(this).val()) {
            case 'Dark 2':
                $('body').removeClass('theme-sltd theme-sltl theme-sdtl').addClass('theme-sdtd');
                break;
            case 'Light 1':
                $('body').removeClass('theme-sdtd theme-sltl theme-sdtl').addClass('theme-sltd');
                break;
            case 'Light 2':
                $('body').removeClass('theme-sltd theme-sdtd theme-sdtl').addClass('theme-sltl');
                break;
            default:
                $('body').removeClass('theme-sltd theme-sltl theme-sdtd').addClass('theme-sdtl');
        }
    });

    $('select[name="page_color"]').on('change', function () {
        $('body').removeClass('color-primary color-dark color-red color-green color-orange color-purple color-blue').addClass('color-' + $(this).val().toLowerCase());
    });

    $('select[name="page_bgcolor"]').on('change', function () {
        $('body').removeClass('bg-clean bg-lighter bg-light-default bg-light-blue bg-light-purple bg-light-dark').addClass('bg-' + $(this).val().toLowerCase());
    });
}