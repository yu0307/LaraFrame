$(document).ready(function(){
    $('#control_CRUD').on('hidden.bs.modal', function (e) {
        $('#control_CRUD').find('.LF_CRUD').removeClass('show');
        $('#control_CRUD input, #control_CRUD textarea, #control_CRUD select').val('').removeClass('checked');
    })
    $('button.outlet_CRUD').on('click',function(){
        if($(this).attr('outlet-target').length>0){
            showCRUD($(this).attr('outlet-target'));
        }
        
    });
});
function showCRUD(tar){
    $('#control_CRUD').find('.'+tar).addClass('show');
    $('#control_CRUD').modal('show');
}