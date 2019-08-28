$(document).ready(function () {
    $('.fe_widget_WidgetTable').on("AjaxUpdated", function (event, data) {
        var target = $('#' + data.target);
        var html = '';
        $(data.data).each(function (idx, d) {
            html += '<tr>';
            $(d).each(function (idx, elm) {
                html += '<td>' + elm + '</td>';
            });
            html += '</tr>';
        })
        html = (html.length <= 0 ? '<h4 class="c-primary text-center text-capitalize align-middle">No data is available...</h4>' : html);
        $(target).find('.panel-content table tbody').html(html);
    });
});