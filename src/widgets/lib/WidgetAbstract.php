<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;
    protected $view=false;

    public function __construct($viewParameters){

        $this->viewParameters=[
            'WidgetName'        =>($viewParameters['WidgetName'] ?? ''),
            'ID'                =>($viewParameters['ID'] ?? ($this->MyName() . '_' . rand(1000, 9000))),
            'DisableHeader'     =>false,
            'DisableFooter'     => true,
            'WidgetBackground'  => 'bg-white',
            'HeaderBackground'  => 'bg-primary',
            'FooterBackground'  => 'bg-dark',
            'HeaderIcon'        => 'star',
            'Widget_header'     => '',
            'Widget_footer'     => '',
            'DisableControls'   => false,
            'Width'             => '3',
            'DataHeight'        => '400',
            'Widget_contents'   => '',
            'WidgetData'        => false,//[false: Display contents only, no data to load. function:function to get data. dataset:var of data]
            'AjaxLoad'          =>false
        ];
        $this->setWidgetType($this->MyName());
        $this->viewParameters['Ajax']['AjaxURL'] = route('WidgetsAjaxPost', ['tarWidget' => $this->MyName(), 'tarControl' => $this->MyID()]); //URL for the generic widget
        $this->viewParameters['Ajax']['AjaxInterval'] = false; //false->load once only, true->global interval with everyone else, number->milliseconds to have it's own timer.
        $this->viewParameters['Ajax']['AjaxType'] = 'POST'; //Request type
        $this->viewParameters = array_merge($this->viewParameters, ($viewParameters ?? []));
    }

    public function MyName(): string{
        return (new \ReflectionClass($this))->getShortName();
    }

    public function WidgetName():string{
        return $this->viewParameters['WidgetName'];
    }

    public function MyID(): string{
        return $this->viewParameters['ID'];
    }

    public function WidgetType(): string{
        return $this->viewParameters['Type'];
    }

    public function setID($name){
        $this->viewParameters['ID']= $name?? $this->viewParameters['ID'];
        return $this;
    }

    protected function setWidgetType($type){
        $this->viewParameters['Type']= $type;
        $this->setAjaxJS($type);   
        return $this;
    }

    protected function setAjaxJS($name,$path= '/feiron/felaraframe/widgets/'){
        $path='/'. trim(trim($path,'/'),'\\').'/';
        $this->viewParameters['Ajax']['AjaxJS'] = asset($path . $name . '.js');
        return $this;
    }

    protected function setWidgetContents($content){
        $this->viewParameters['Widget_contents'] = $content;
        return $this;
    }

    protected function setView($viewName){
        $this->view=view($viewName);
        return $this;
    }

    public function setData($data){
        $this->viewParameters['WidgetData'] = (is_callable($data)) ? $data() : $data;
        return $this;
    }

    //render final widget html to the pipeline
    public function render(){
        
        if($this->viewParameters['AjaxLoad']===false && $this->viewParameters['WidgetData']!==false){
            $this->viewParameters['WidgetData'] = (is_callable($this->viewParameters['WidgetData'])) ? $this->viewParameters['WidgetData']() : $this->dataFunction();
            if (empty($this->viewParameters['WidgetData'])) {
                $this->setWidgetContents('<h4 class="c-primary text-center text-capitalize align-middle">No data is available...</h4>');
            }
        }
        
        $this->viewParameters['WidgetName'] = $this->WidgetName();
        return (false=== $this->view? View::make('fe_widgets::widgetFrame', $this->viewParameters): $this->view->with($this->viewParameters))->render();
    }

    //send ajax data to the client
    public function renderAjax($request){
        return response()->json(['target'=>$this->MyID(),'widget_type'=>$this->WidgetType(),'data' => $this->getAjaxData($request)]);
    }

    public function getWidgetSettings(){
        $settingList= ['ID', 'DataHeight'];
        if($this->viewParameters['AjaxLoad']!==false){
            $settingList = array_merge($settingList,['AjaxLoad', 'Ajax']);
        }
        return collect($this->viewParameters)->only($settingList)->toArray();
    }

    //responsible for polymorphic classes to build their ajax data
    public function getAjaxData($request)
    {
        return $this->dataFunction();
    }
    
    //responsible for building widget specific data as part of the widget output. for parameter [WidgetData]
    public abstract function dataFunction();
}

?>