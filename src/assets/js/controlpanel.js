$(document).ready(function () {
    $('#control_CRUD').on('hidden.bs.modal', function (e) {
        $('#control_CRUD').find('.LF_CRUD').removeClass('show');
        $('#control_CRUD input:not([type="radio"],[type="checkbox"]), #control_CRUD textarea, #control_CRUD select').val('').removeClass('checked').trigger('change');
    })
    $('button.outlet_CRUD').on('click', function () {
        if ($(this).attr('outlet-target').length > 0) {
            showCRUD($(this).attr('outlet-target'));
        }
    });
});

function hideCRUD(callback) {
    $('#control_CRUD').modal('hide');
    if (typeof (callback) == 'function') {
        callback();
    }
}

function showCRUD(tar, preload = false) {
    $('#control_CRUD').find('.' + tar).addClass('show');
    if (preload) {
        $('#control_CRUD .loading').addClass('show');
    } else {
        $('#control_CRUD .loading').removeClass('show');
    }
    $('#control_CRUD').modal('show');
}

function buildData(tar, callback) {
    var data = {};
    $.each($(tar).find('.form-control').serializeArray(), function (idx, elm) {
        data[elm['name']] = elm['value'];
    });
    if (typeof (callback) === 'function') {
        callback(data);
    }
    return data;
}