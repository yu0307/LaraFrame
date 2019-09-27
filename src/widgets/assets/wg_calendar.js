$(document).ready(function () {
    wg_calendar_activate($(".fe_widget .wg_calendar"));
    $(document).on("wg_added", '.fe_widget', function (event, data, ID) {
        wg_calendar_activate($(this).find(".wg_calendar"));
    });
});

function wg_calendar_activate(tar){
    $(tar).datepicker({
        prevText: '<i class="fa fa-fw fa-angle-left"></i>',
        nextText: '<i class="fa fa-fw fa-angle-right"></i>'
    });
}

