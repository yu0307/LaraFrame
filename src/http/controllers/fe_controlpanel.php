<?php

namespace feiron\felaraframe\http\controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use feiron\felaraframe\models\LF_MetaInfo;

class fe_controlpanel extends Controller
{
    public function __construct(){

    }

    public function show(Request $request){
        return view('felaraframe::controlPanel');
    }

    public function UpdateTheme(Request $request){
        if($request->filled('ThemeSelected')){
            if(app()->FeFrame->GetCurrentTheme()->name()!==$request->input('ThemeSelected')){
                LF_MetaInfo::updateOrCreate(['meta_name'=> 'theme'],['meta_value'=> get_class(app()->FeFrame->getThemeByName($request->input('ThemeSelected')))]);
            }
            LF_MetaInfo::updateOrCreate(
                ['meta_name' => 'themeSetting'],
                ['meta_value' => $request->input('themeSetting')]
            );
        }
        return ['status' => 'success', 'message' => ['Theme Settings updated']];
    }

    public function LoadThemeInfo(Request $request, $ThemeName){
        $themeInfo=app()->FeFrame->getThemeByName($ThemeName);
        
        if(!isset($themeInfo)){
            return ['status' => 'error', 'message' => ['No such theme in the system.']];
        }else{
            $themeInfo= $themeInfo->themeSettings();
        }
        return ['status' => 'success', 'message' => ['Theme loaded'],'settingList'=> $themeInfo,'siteDefaults'=>($ThemeName== app()->FeFrame->GetCurrentTheme()->name()? app()->FeFrame->GetThemeSettings():[])];
    }

    public function SaveSettings(Request $request){
        if ($request->filled('Setting')) {
            LF_MetaInfo::updateOrCreate(
                ['meta_name' => 'SiteSetting'],
                ['meta_value' => $request->input('Setting')]
            );
        }
        return ['status' => 'success', 'message' => ['Site Settings updated']];
    }
}
