<?php
namespace feiron\felaraframe\lib\helper;
use Illuminate\Support\Facades\Route;
use feiron\felaraframe\models\LF_Mails;
class Communication {
    private $notifications=[];
    public static function getMessage($type='new'){
        return LF_Mails::where(['recipient'=>auth()->user()->getKey(),'status'=>$type])->orderBy('created_at','DESC')->get();
    }
    public static function sendMessage($recepient,$title,$message,$sender,$remark=null){
        try {
            LF_Mails::create([
                'sender'=>$sender,
                'recipient'=>$recepient,
                'subject'=>$title,
                'contents'=>$message,
                'remarks'=>$remark
                ]);
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
