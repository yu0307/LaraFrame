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
        userWidgetLayout::updateOrCreate([
            'layoutable_id'=> auth()->user()->id,
            'layoutable_type' => get_class(auth()->user())
        ],[
            'widget_name'=>json_encode($request->input('layout')),
            'settings' => json_encode($request->input('settings')??[])
        ]);

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Layout Updated.']);
        }
    }
}
