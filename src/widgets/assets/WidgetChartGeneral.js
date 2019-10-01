
$(document).ready(function () {

    activate($('.fe_widget .wg_Charts'));

    $(document).on("wg_added", '.fe_widget ', function (event, data, ID) {
        activate($(this).find('.wg_Charts'));
    });

    function activate(tar) {
        $(tar).each(function (index, elm) {
            if (false !== $.inArray('widgetConfig', Object.keys(DashBoardWidgetBank['wg_' + $(elm).attr('wg_id')]))) {
                var setting = DashBoardWidgetBank['wg_' + $(elm).attr('wg_id')].widgetConfig.chartSetting;
                setting.element = $(elm).attr('id');
                var keys = Object.keys(setting.data[0]);
                if (undefined == setting.xkey) setting.xkey = keys[0];
                if (undefined == setting.ykeys) setting.ykeys = [keys[1]];
                if (undefined == setting.labels) setting.labels = [keys[1]];
                if (setting.AjaxLoad !== undefined && AjaxLoad === true) {
                    setting.data = [];
                }
                switch ($(elm).attr('chartType')) {
                    case "bar":
                        DashBoardWidgetBank['wg_' + $(elm).attr('wg_id')].instance = new Morris.Bar(setting);
                        break;
                    case "donut":
                        DashBoardWidgetBank['wg_' + $(elm).attr('wg_id')].instance = new Morris.Donut(setting);
                        break;
                    case "area":
                        DashBoardWidgetBank['wg_' + $(elm).attr('wg_id')].instance = new Morris.Area(setting);
                        break;
                    default://line
                        DashBoardWidgetBank['wg_' + $(elm).attr('wg_id')].instance = new Morris.Line(setting);
                        break;
                }
            }
        });
    }
});