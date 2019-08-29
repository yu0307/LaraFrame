<?php

namespace feiron\felaraframe\widgets;

class WidgetManager {
    private $app;
    private $UserWidgetList;    //Array of keys for widget names
    private $AvailableWidgets;  //[WidgetDisplayName]=>Settings['widgetType','Description','widgetParam']

    public function __construct(\Illuminate\Foundation\Application $app){
        $this->app = $app;
        $this->AvailableWidgets=[];
        $this->UserWidgetList=[];
    }

    //Add widgets to site's available widgets pool
    public function addWidget($widgetName,$Param=[]){
        $this->AvailableWidgets[$widgetName]= array_merge(['widgetType' => 'WidgetGeneric', 'Description' => '', 'widgetParam' => []],($Param??[])) ;
    }

    //remove widgets from site's available widgets pool
    public function removeWidget($widgetName){
        unset($this->AvailableWidgets[$widgetName]);
    }

    public function addToUserWidgetList($widgetName){
        array_push($this->UserWidgetList,$widgetName);
    }

    public function updateUserWidgetList($New_UserWidgetList){
        $this->UserWidgetList=$New_UserWidgetList;
        //update DB layout
        //update Cookies layout
    }

    public function renderWidgets(){
        $cnt='';
        foreach($this->UserWidgetList as $widget){
            if(!empty($this->AvailableWidgets[$widget]['widgetType'])){
                $cnt .= app()->Widget->BuildWidget($this->AvailableWidgets[$widget]['widgetType'], ($this->AvailableWidgets[$widget]['widgetParam'] ?? []))->render();
            }
        }
        return $cnt;
    }
}
