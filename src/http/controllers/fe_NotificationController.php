<?php

namespace feiron\felaraframe\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use feiron\felaraframe\models\LF_Mails;

class fe_NotificationController extends Controller
{
    public function loadNotification(Request $request, $MID){
        $mail=LF_Mails::find($MID);
        $mail->status='read';
        $mail->save();
        return response()->json($mail->load('Sender')->toArray());
    }

    public function geAllNotifications(Request $request){
        return response()->json(['status'=>'success','messages'=>LF_Mails::getMails(auth()->user()->getKey())->loadMissing('Sender')]);
    }

    public function removeNotification(Request $request, $MID){
        LF_Mails::destroy($MID);
        return response()->json(['status'=>'success','message'=>['Message Removed.']]);
    }
}
