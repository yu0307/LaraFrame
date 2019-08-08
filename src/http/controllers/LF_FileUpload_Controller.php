<?php

namespace feiron\felaraframe\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LF_FileUpload_Controller extends Controller
{
    public function processUploads(Request $request){
        $validator = Validator::make($request->all(), [
            'LF_FilesUpload' =>('required|file')
        ]);
        if ($validator->fails()) {
            return ['status' => 'error', 'message' => $validator->getMessageBag()->toArray()];
        }else{
            $path=$request->LF_FilesUpload->store('LF_FilesUpload/'.date('Ymdhis'));
        }
        return ['status' => 'success', 'message' => ['File uploaded.'],'datapath'=>$path];
    }
}
