<?php
namespace feiron\felaraframe\lib\traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use feiron\felaraframe\lib\traits\error;

trait uploader{
    /* 
    use Variables: 
        -fieldName
        -validatorConfig 
    */
    use error;
    abstract public function PreUploadPorcess(Request $request,$files=null,&$path=null);
    abstract public function postUploadPorcess($response);

    public function processUpload(Request $request){
        $response=[];
        $validatorConfig=$this->validatorConfig??'max:6200|mimes:jpg,png,audio/mp3,video/mp4';
        $fieldName=($this->fieldName??'upload');
        $files = $request->file($fieldName);
        if(is_array($files)){
            $fieldName.='.*';
        }
        $validator = Validator::make($request->all(), [
            $fieldName =>($validatorConfig)
        ]);
        if ($validator->fails()) {
            $this->sendError($validator);
        }else{
            $path=(config('felaraframe.appconfig.upload_path')??'temp/usrUploads/').$request->user()->id;
            $this->PreUploadPorcess($request,$files, $path);
            $response=$this->UploadPorcess($request,(is_array($files)?$files:[$files]),$path);          
        }
        $this->postUploadPorcess($response);
        return ['uploadedFiles' => $response,'success'=>['message'=>'File Uploaded.']];
    }

    public function UploadPorcess($request,$files,$path){
        $response=[];
        foreach((is_array($files)?$files:[$files]) as $file){
            $fileName=$request->user()->id.date('Ymdhis').$file->getClientOriginalName();
            $file->move(public_path($path), $fileName);
            array_push($response,['name'=>$file->getClientOriginalName(),'path'=>$path,'fileName'=>$fileName,'MIME'=>$file->getClientMimeType(),'url'=>URL::asset($path.'/'.$fileName)]);
        }
        return $response;
    }
}

?>