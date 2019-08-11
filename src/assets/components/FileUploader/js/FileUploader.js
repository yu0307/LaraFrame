$(function(){

    var ul = $('.LF_form_uploads ul');

	$( ".LF_form_uploads .dropzone" ).on('dragover', function (e) {
		// Prevent the default browser drop action:
		$( ".LF_form_uploads .dropzone" ).addClass('in');
		e.preventDefault();	
	});

	$( ".LF_form_uploads .dropzone" ).on('dragleave', function (e) {
		// Prevent the default browser drop action:
		$( ".LF_form_uploads .dropzone" ).removeClass('in');
		e.preventDefault();
    });
    
    $('.LF_form_uploads .dropzone .LF_UploadBrowse').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $(this).parent().find('input[type="file"]').click();
    });

    // Initialize the jQuery File Upload plugin
    $('.LF_form_uploads').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: $('.LF_form_uploads .dropzone '),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {            
            var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

            // Append the file name and file size
            tpl.find('p').text(data.files[0].name)
                         .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);
			setTimeout(function(){$(tpl).fadeOut(1000,'linear');},2500);

            // Initialize the knob plugin
            tpl.find('input').knob();

            // Listen for clicks on the cancel icon
            tpl.find('span').click(function(){
                if(tpl.hasClass('working')){
                    jqXHR.abort();
                }
                tpl.fadeOut(function(){
                    tpl.remove();
                });
            });

			var jqXHR;
            // Automatically upload the file once it is added to the queue
			$('.upload_info').hide();
			$('.upload_info .upload_info_content').html('');
			$('.upload_info').slideDown(1000,'linear',function(){jqXHR = data.submit();});
            $(".LF_form_uploads .dropzone" ).removeClass('in');
            $('.LF_form_uploads').trigger('LF_Files_added',data);
        },

		done: function (e, data) {
                $('.upload_info_loading').fadeOut(1000,'linear',function(){
                    $('.upload_info .upload_info_content').append('<div>Upload Complete.</div>');
                    $('.LF_form_uploads').trigger('LF_Files_Uploaded',data);
                });
		},

        progress: function(e, data){
            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);
            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();
            if(progress === 100){
                data.context.removeClass('working');
            }
            $('.LF_form_uploads').trigger('LF_Files_processing',data);
        },

        fail:function(e, data){
            // Something has gone wrong!
            data.context.addClass('error');
        }

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

function setUploadInfo(info){
    $('.upload_info .upload_info_content').append(info);
}