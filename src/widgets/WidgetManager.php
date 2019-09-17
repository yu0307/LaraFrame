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
        $this->AvailableWidgets=[
            'clock'=>['widgetType' => 'wg_clock', 'Description' => 'showing a clock on the dashboard'],
            'calendar' => ['widgetType' => 'wg_calendar', 'Description' => 'A simple calendar widget.'],
            'weather' => ['widgetType' => 'wg_weather', 'Description' => 'A simple widget shows current weather forecast at your location.']
        ];
        $this->UserWidgetList=[];
    }

    public function getSiteWidgetList(){
        return $this->AvailableWidgets;
    }

    public function getSiteWidgetDetail($widgetName){
        if(array_key_exists($widgetName,$this->AvailableWidgets)===true){
            return array_merge($this->AvailableWidgets[$widgetName], ['userSettingOutlet'=>app()->Widget->getWidgetSettingOutlet($this->AvailableWidgets[$widgetName]['widgetType'])]);
        }
        return [];
    }

    public function loadLayout($user){
        $layout = userWidgetLayout::where('layoutable_id', $user->id)->first();
        $this->UserWidgetList = (json_decode($layout->widget_name??false)??$this->UserWidgetList);
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

    public function UpdateWidgetLayout($layout_array=[],$setting_array=[]){
        userWidgetLayout::updateOrCreate([
            'layoutable_id' => auth()->user()->id,
            'layoutable_type' => get_class(auth()->user())
        ], [
            'widget_name' => json_encode($layout_array),
            'settings' => json_encode($setting_array)
        ]);
    }

    public function renderUserWidgets($user){
        $this->loadLayout($user??auth()->user());
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
