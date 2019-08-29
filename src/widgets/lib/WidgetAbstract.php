<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;
    protected $view=false;

    public function __construct($viewParameters){
        $this->setWidgetType($this->MyName());
        $this->viewParameters['HeaderBackground'] = 'bg-primary';
        $this->viewParameters['FooterBackground'] = 'bg-dark';
        $this->viewParameters['HeaderIcon'] = 'star';
        $this->viewParameters['Widget_header'] = '';
        $this->viewParameters['Widget_footer'] = '';
        $this->viewParameters['DisableControls'] = false;
        $this->viewParameters['Width'] = '4';
        $this->viewParameters['DataHeight'] = '400';
        $this->viewParameters['Widget_contents'] = '';
        $this->viewParameters['widgetData'] = ''; //used by polymorphic classes to set their data.
        $this->viewParameters['AjaxLoad'] = false; //If data should be loaded via Ajax request.
        $this->SetID($viewParameters['ID'] ?? ($this->MyName() . '_' . rand(1000, 9000)));
        $this->viewParameters['Ajax']['AjaxURL'] = route('WidgetsAjaxPost', ['tarWidget' => $this->MyName(), 'tarControl' => $this->MyID()]); //URL for the generic widget
        $this->viewParameters['Ajax']['AjaxInterval'] = false; //false->load once only, true->global interval with everyone else, number->milliseconds to have it's own timer.
        $this->viewParameters['Ajax']['AjaxType'] = 'POST'; //Request type
        $this->viewParameters = array_merge($this->viewParameters, ($viewParameters ?? []));
    }

    public function MyName(): string{
        return (new \ReflectionClass($this))->getShortName();
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
        $this->viewParameters['widgetData'] = (is_callable($data)) ? $data() : $data;
        return $this;
    }

    //render final widget html to the pipeline
    public function render(){
        if($this->viewParameters['AjaxLoad']===false){
            $this->viewParameters['widgetData'] = (is_callable($this->viewParameters['widgetData'])) ? $this->viewParameters['widgetData']() : $this->dataFunction();
            if (empty($this->viewParameters['widgetData'])) {
                $this->setWidgetContents('<h4 class="c-primary text-center text-capitalize align-middle">No data is available...</h4>');
            }
        }
        return (false=== $this->view?view('fe_widgets::widgetFrame', $this->viewParameters)->render():$this->view->with($this->viewParameters)->render());
    }

    //send ajax data to the client
    public function renderAjax(){
        return response()->json(['target'=>$this->MyID(),'widget_type'=>$this->WidgetType(),'data' => $this->getAjaxData()]);
    }

    //responsible for polymorphic classes to build their ajax data
    public abstract function getAjaxData();
    
    //responsible for building widget specific data as part of the widget output. for parameter [widgetData]
    public abstract function dataFunction();
}

?>