<?php

namespace FeIron\LaraFrame\http\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class fe_homeController extends Controller
{
    public function __construct()
    {
        
    }

    public function home(){
        return view('LaraFrame::home');
    }
}
