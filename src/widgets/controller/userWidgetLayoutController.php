<?php

namespace feiron\felaraframe\widgets\controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use feiron\felaraframe\widgets\models\userWidgetLayout;

class userWidgetLayoutController extends Controller
{
    public function UpdateWidgetLayout(Request $request){
        $request->validate([
            'layout' => 'required'
        ]);
        
        app()->WidgetManager->UpdateWidgetLayout($request->input('layout'),($request->input('settings') ?? []));

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Layout Updated.']);
        }
    }
}
