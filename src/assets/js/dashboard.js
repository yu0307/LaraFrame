$(document).ready(function () {
    Sortable.create(fe_widgetCtrls, {
        animation: 150,
        sort: true,
        filter: ".feWidgetFixed",  // Selectors that do not lead to dragging (String or Function)
        draggable: ".fe_widget",  // Specifies which items inside the element should be draggable
        easing: "cubic-bezier(0.075, 0.82, 0.165, 1)",
        dragClass: "sortable_drag",  // Class name for the dragging item
        ghostClass: 'sortGhosting'
    });

    $('#new_widget_area #widget_add').on('click', function () {
        $('#new_widget_area #widget_add_win').addClass('activated');
    });
});

