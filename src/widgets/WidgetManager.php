<?php

namespace feiron\felaraframe\widgets;

use Illuminate\Support\Facades\Auth;
use feiron\felaraframe\widgets\models\userWidgetLayout;

class WidgetManager {
    private $app;
    private $AvailableWidgets;  //[WidgetDisplayName]=>Settings['widgetType','Description','widgetParam']

    public function __construct(\Illuminate\Foundation\Application $app){
        $this->app = $app;
        $this->AvailableWidgets=[//available generic widgets to users for selection.
            'clock'=>['widgetType' => 'wg_clock', 'Description' => 'showing a clock on the dashboard'],
            'calendar' => ['widgetType' => 'wg_calendar', 'Description' => 'A simple calendar widget.'],
            'weather' => ['widgetType' => 'wg_weather', 'Description' => 'A simple widget shows current weather forecast at your location.']
        ];
        $this->UserWidgetList= $this->UserWidgetSetings=[];
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

    private function loadLayout($user){
        return userWidgetLayout::where('layoutable_id', $user->id)->orderBy('order', 'asc')->get();
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

    public function addToUserWidgetList($widgetName,$settings=[]){
        array_push($this->UserWidgetList,   $widgetName);
        array_push($this->UserWidgetSetings, $settings);
    }

    public function UpdateWidgetLayout($layout_array=[]){
        foreach($layout_array as $index=>$key){
            userWidgetLayout::find($key)
                            ->update(['order' => $index+1]);
        }
        userWidgetLayout::where('layoutable_id', auth()->user()->id)->whereNotIn('id', $layout_array)->delete();
    }

    public function UpdateUserWidgetSettings($target,$setting){
        userWidgetLayout::find($target)
            ->update(['settings' => $setting]);
    }

    public function addToLayout($widget){
        $UID= auth()->user()->id;
        $counter = (userWidgetLayout::where('layoutable_id', $UID)->max('order')??0)+1;
        return userWidgetLayout::create([
            'layoutable_id' => $UID,
            'layoutable_type' => get_class(auth()->user()),
            'widget_name' => $widget['name'],
            'order'=> $counter,
            'settings'=> json_encode($widget['setting'])
        ]);
    }

    public function renderUserWidgets($user){
        $cnt='';
        $SourceList = [];
        foreach($this->loadLayout($user ?? auth()->user()) as $widget){
            if(!empty($this->AvailableWidgets[$widget->widget_name]) && !empty($this->AvailableWidgets[$widget->widget_name]['widgetType'])){
                $usrSetting= array_merge(($this->AvailableWidgets[$widget->widget_name]['widgetParam'] ?? []), (json_decode($widget->settings,true) ?? []));
                $usrSetting['usr_key']= $widget->id;
                $Tempwidget=app()->Widget->BuildWidget(
                                                        $this->AvailableWidgets[$widget->widget_name]['widgetType'], 
                                                        $usrSetting
                                                    );
                foreach(array_merge($Tempwidget->getHeaderScripts(), $Tempwidget->getFooterScripts()) as $resource){
                    if($resource['duplicate']===false && in_array($resource['file'], $SourceList)){
                        $Tempwidget->removeResource($resource['file']);
                    }
                    if (!in_array($resource['file'], $SourceList)){
                        array_push($SourceList, $resource['file']);
                    }
                }
                $cnt .= $Tempwidget->render();
            }
        }
        return $cnt;
    }

    public function renderUserWidget($userWidgetName,$asResource=false,$widgetUserSettings=[]){//For ajax rendering use
        
        $widget= app()->Widget->BuildWidget(
                                                $this->AvailableWidgets[$userWidgetName]['widgetType'], 
                                                ($this->AvailableWidgets[$userWidgetName]['widgetParam'] ?? [])
                                            );
        if(!empty($widgetUserSettings))$widget->UpdateWidgetSettings($widgetUserSettings);
        $resources=[
            'scripts'=>array_merge($widget->getHeaderScripts(), $widget->getFooterScripts()),
            'styles'=> array_merge($widget->getHeaderStyle(), $widget->getFooterStyle())
        ];
        return (($asResource === false)? $widget->render(): [
            'html' => $widget->render(),
            'settings' => array_merge($widget->getWidgetSettings(),$resources)
        ]) ;
    }
}
