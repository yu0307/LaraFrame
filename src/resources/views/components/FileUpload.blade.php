<form class="LF_form_uploads" form_type="LF_form_uploads" enctype="multipart/form-data" action="{{(isset($action)?$action:route('LF_FileUploads'))}}" id="{{$id??'LF_fileUploads_form'}}">
    
    <div id="drop" class="dropzone">
        {{$slot??'Drop List of Files Here<br/>'}}
        <a class="btn btn-primary btn-sm LF_UploadBrowse">Browse</a>
        <input type="file" name="LF_FilesUpload" {{($multiple??false)?'multiple':''}}/>
    </div>

    <ul>
        <!-- The file uploads will be shown here -->
    </ul>
    <div class="clearfix"></div>
    <div class="fallback">

    </div>
</form>
<div class="upload_info" id="upload_info">
    <div id="upload_info_loading" class="upload_info_loading">
        <i class="fa fa-spinner fa-spin fa-fw"></i>&nbsp;&nbsp; <span id="proc_cnt">Processing ...</span>
    </div>
    <div id="upload_info_content" class="upload_info_content">

    </div>
</div>

@pushonce('headerstyles','FeFileUpload')
<link type="text/css" href="{{asset('/feiron/felaraframe/components/FileUploader/css/FileUploader.css')}}" rel="stylesheet"> <!-- upload css -->
@endpushonce

@pushonce('footerscripts','FeFileUpload')
<script type="text/javascript" src="{{asset('/feiron/felaraframe/components/FileUploader/js/FileUploader.js')}}"></script> <!-- upload control script -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/mini-upload-form/assets/js/jquery.fileupload.js')}}"></script> <!-- Upload Contorls -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/mini-upload-form/assets/js/jquery.iframe-transport.js')}}"></script> <!-- Upload Contorls -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/mini-upload-form/assets/js/jquery.knob.js')}}"></script> <!-- Upload Contorls -->
<script type="text/javascript" src="{{asset('/feiron/felaraframe/plugins/mini-upload-form/assets/js/jquery.ui.widget.js')}}"></script> <!-- Upload Contorls -->
@endpushonce