<?php

namespace feiron\felaraframe\widgets\controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use feiron\felaraframe\widgets\models\userWidgetLayout;

class userWidgetLayoutController extends Controller
{
    
    public function addWidget(Request $request, $WidgetName){
        $layoutItem=app()->WidgetManager->addToLayout(['name'=> $WidgetName, 'setting'=> ($request->input('userSetting') ?? [])]);
        return response()->json(app()->WidgetManager->renderUserWidget($WidgetName, true, array_merge(($request->input('userSetting') ?? []),['usr_key'=> $layoutItem->id])));
    }

    public function UpdateWidgetLayout(Request $request){

        app()->WidgetManager->UpdateWidgetLayout($request->input('newLayout')??[]);
        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Layout Updated.']);
        }
    }

    public function updateUserWidgetSetting(Request $request){
        $request->validate([
            'target' => 'required',
            'Settings' => 'required'
        ]);
        app()->WidgetManager->UpdateUserWidgetSettings($request->input('target'), json_encode($request->input('Settings') ?? []));

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'widget setting Updated.']);
        }
    }
}
