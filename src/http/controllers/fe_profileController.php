<?php

namespace feiron\felaraframe\http\controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class fe_profileController extends Controller
{
    public function show(Request $request, $uid = null)
    {
        $userProvider = config('auth.providers.' . config('auth.guards.web.provider') . '.model');
        return view('felaraframe::profile', ['User' => (empty($uid) ? auth()->user() : $userProvider::find($uid)), 'Editable' => empty($uid)]);
    }
}
