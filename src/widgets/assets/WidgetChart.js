$(document).ready(function () {
    $(document).on("AjaxUpdated", '.fe_widget_WidgetChart', function (event, data, ID) {
        DashBoardWidgetBank['wg_' + ID].instance.setData(data.data);
    });
});