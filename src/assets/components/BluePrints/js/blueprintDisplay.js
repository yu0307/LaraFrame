$(document).ready(function(){
    $('.collection_item .btn').on('click', function(){
        $(this).parents('tr').next('tr.hiddenItems').find('td.'+$(this).attr('target')).slideDown(100);
    });
    $('.btn.closeHidden').on('click', function(){
        $(this).parents('td.hidden_content').slideUp(100);
    });
});