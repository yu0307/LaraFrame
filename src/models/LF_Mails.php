<?php

namespace feiron\felaraframe\models;

use Illuminate\Database\Eloquent\Model;

class LF_Mails extends Model
{
    protected $table= 'lf_mail';

    protected $fillable=['sender', 'recipient', 'subject', 'contents', 'remarks'];

    protected $appends = ['send_time'];

    public function getSendTimeAttribute(){
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('(D)M-d Y');
    }

    public function Recipient(){
        return $this->belongsTo(config('auth.providers.' . config('auth.guards.web.provider') . '.model'), 'recipient');
    }

    public function Sender(){
        return $this->belongsTo(config('auth.providers.' . config('auth.guards.web.provider') . '.model'), 'sender');
    }

    public static function getMails($recepient=null){
        return (empty($recepient)? LF_Mails::orderBy('created_at', 'desc')->get(): LF_Mails::where('recipient',$recepient)->orderBy('created_at', 'desc')->get());
    }

}
