<?php
namespace feiron\felaraframe\lib\helper;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeFrameHelper {

    public function GetProfileImage($size=60,$sourceOnly=false, $user_profile_pic = null){
        $user=Auth::user();
        
        $rst= !empty($user->profile_image) ? Storage::url($user->profile_image) : ($user_profile_pic ?? ("https://www.gravatar.com/avatar/".md5(strtolower( trim($user->email ) ))."?d=".(asset('/feiron/felaraframe/images/avatars/avatar7.png'))."&s=".($size??60)));
        if($sourceOnly===true){
            $rst= '<img src="'. $rst. '" alt="user image">';
        }
        return $rst;
    }
}
