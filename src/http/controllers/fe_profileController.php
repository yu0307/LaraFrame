<?php

namespace feiron\felaraframe\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class fe_profileController extends Controller
{
    public function show(Request $request, $uid=null){
        $userProvider = config('auth.providers.' . config('auth.guards.web.provider') . '.model');
        return view('felaraframe::profile',['User'=> (empty($uid)? auth()->user(): $userProvider::find($uid)), 'Editable'=> empty($uid)]);
    }

    public function UploadProfImg(Request $request){
        
    }
}
