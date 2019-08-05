$(function(){

    var ul = $('#upload ul');

    // Initialize the jQuery File Upload plugin
    $( "#control_area .filemanager #Drop_uploader" ).fileupload({
        dropZone:  $( "#control_area .filemanager #Drop_in" ),
        add: function (e, data) {
			if($('#Base_URL').val()=='INTERCHANGE FORMS'){//if($('#Base_URL').val().indexOf('INTERCHANGE FORMS')>=0)
            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');
            tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

//            // Add the HTML to the UL element
//            data.context = tpl.appendTo(ul);
//			setTimeout(function(){$(tpl).fadeOut(1000,'linear');},2500);
            tpl.find('input').knob();
			init_progress(0);
			data.url=myAjax.ajaxurl+'?action=EX_Script_Drop_upload&URL='+$('#Base_URL').val();
            var jqXHR = data.submit();
			}
			else{
				$( "#control_area .filemanager #Drop_in" ).removeClass('in');
				Messenger().post({
						message: '<h4 style="text-align:center">You can only use external dropping towards INTERCHANGE FORMS Folder.</h4>',
						type: 'error', //info, error or success
						showCloseButton: true,
					});	
			}
        },
		done: function (e, resp) {
			var tmp=JSON.parse(resp.result);
			if(tmp.status!='success'){
				Messenger().post({
						message: tmp.msg,
						type: 'error', //info, error or success
						showCloseButton: true,
					});	
				}
			},

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);
			
			$("#Progress_Loader").percentageLoader({value: (parseInt(data.loaded/100000,10)+'kb'), progress: (data.loaded / data.total)});

            if(progress == 100){
					$('#EX_center_common_diag').modal('hide');
					$( "#control_area .filemanager #Drop_in" ).removeClass('in');
            }
        },

        fail:function(e, data){
            // Something has gone wrong!
            data.context.addClass('error');
        }

    });


    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }

});