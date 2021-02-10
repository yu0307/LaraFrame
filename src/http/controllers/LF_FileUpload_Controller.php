<?php

namespace feiron\felaraframe\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use feiron\felaraframe\lib\traits\uploader;

class LF_FileUpload_Controller extends Controller
{
    use uploader;
    private $fieldName='fileToUpload';

    public function PreUploadPorcess(Request $request,$files=null,&$path=null)
    {
        return false;
    }

    public function postUploadPorcess($response){
        return false;
    }
}
