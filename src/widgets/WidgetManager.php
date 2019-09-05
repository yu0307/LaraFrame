<?php

namespace feiron\felaraframe\widgets;

use Illuminate\Support\Facades\Auth;
use feiron\felaraframe\widgets\models\userWidgetLayout;

class WidgetManager {
    private $app;
    private $UserWidgetList;    //Array of keys for widget names
    private $AvailableWidgets;  //[WidgetDisplayName]=>Settings['widgetType','Description','widgetParam']

    public function __construct(\Illuminate\Foundation\Application $app){
        $this->app = $app;
        $this->AvailableWidgets=[];
        $this->UserWidgetList=[];
    }

    public function getSiteWidgetList(){
        return $this->AvailableWidgets;
    }

    public function loadLayout($user){
        $layout = userWidgetLayout::where('layoutable_id', $user->id)->first()->toArray();
        $layout=json_decode($layout['widget_name']);
        $this->UserWidgetList = $layout ?? [];
    }

    //Add widgets to site's available widgets pool
    public function addWidget($widgetName,$Param=[]){
        $Param= $Param ?? [];
        $Param['widgetParam']['WidgetName']= $widgetName;
        $this->AvailableWidgets[$widgetName]= array_merge(['widgetType' => 'WidgetGeneric', 'Description' => ''], $Param) ;
    }

    //remove widgets from site's available widgets pool
    public function removeWidget($widgetName){
        unset($this->AvailableWidgets[$widgetName]);
    }

    public function addToUserWidgetList($widgetName){
        array_push($this->UserWidgetList,$widgetName);
    }

    // public function updateUserWidgetList($New_UserWidgetList){
    //     $this->UserWidgetList=$New_UserWidgetList;
    //     //update DB layout
    //     //update Cookies layout
    // }

    public function renderUserWidgets($user){
        $this->loadLayout($user);
        $cnt='';
        foreach($this->UserWidgetList as $widget){
            if(!empty($this->AvailableWidgets[$widget]) && !empty($this->AvailableWidgets[$widget]['widgetType'])){
                $cnt .= app()->Widget->BuildWidget($this->AvailableWidgets[$widget]['widgetType'], ($this->AvailableWidgets[$widget]['widgetParam'] ?? []))->render();
            }
        }
        return $cnt;
    }

    public function renderUserWidget($userWidgetName,$asResource=false){
        
        $widget= app()->Widget->BuildWidget($this->AvailableWidgets[$userWidgetName]['widgetType'], ($this->AvailableWidgets[$userWidgetName]['widgetParam'] ?? []));
        return (($asResource === false)? $widget->render(): [
            'html' => $widget->render(),
            'settings' => $widget->getWidgetSettings()
        ]) ;
    }

    public function getUserWidgetSettings($userWidgetName){

    }
}
