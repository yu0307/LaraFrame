<?php

namespace feiron\felaraframe\http\controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class fe_controlpanel extends Controller
{
    public function __construct(){

    }

    public function show(Request $request){
        return view('felaraframe::controlPanel');
    }
}
