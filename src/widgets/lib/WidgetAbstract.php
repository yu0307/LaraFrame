<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;
    protected $view=false;

    public function MyName(){
        return (new \ReflectionClass($this))->getShortName();
    }

    public function MyID(){
        return $this->viewParameters['ID'];
    }

    public function SetID($name){
        $this->viewParameters['ID']= $name?? $this->viewParameters['ID'];
        return $this;
    }

    public function __construct($viewParameters){
        $this->viewParameters['Type'] = $this->MyName();
        $this->viewParameters['HeaderBackground'] = 'bg-primary';
        $this->viewParameters['FooterBackground'] = 'bg-dark';
        $this->viewParameters['HeaderIcon'] = 'star';
        $this->viewParameters['Widget_header'] = '';
        $this->viewParameters['Widget_footer'] = '';
        $this->viewParameters['DisableControls'] = false;
        $this->viewParameters['Width'] = '4';
        $this->viewParameters['DataHeight'] = '400';
        $this->viewParameters['Widget_contents'] = '';
        $this->viewParameters['widgetData']='';//used by polymorphic classes to set their data.

        $this->viewParameters['Ajaxload'] = false; //If data should be loaded via Ajax request.
        $this->SetID($viewParameters['ID']??($this->MyName() . '_' . rand(1000, 9000)));
        $this->viewParameters['Ajax']['AjaxURL'] = route('WidgetsAjaxPost', ['tarWidget'=>$this->MyName(), 'tarControl'=>$this->MyID()]); //URL for the generic widget
        $this->viewParameters['Ajax']['AjaxInterval'] =false; //false->load once only, true->global interval with everyone else, number->milliseconds to have it's own timer.
        $this->viewParameters['Ajax']['AjaxType']='POST'; //Request type
        $this->viewParameters['Ajax']['AjaxJS'] = $this->setAjaxJS($this->MyName());
        $this->viewParameters = array_merge($this->viewParameters, ($viewParameters ?? []));        
    }

    protected function setAjaxJS($name,$path= '/feiron/felaraframe/widgets/'){
        $path='/'. trim(trim($path,'/'),'\\').'/';
        return asset($path . $name . '.js');
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
        $this->viewParameters['Widget_contents'] = (is_callable($data)) ? $data() : $data;
        return $this;
    }

    //render final widget html to the pipeline
    public function render(){
        $this->viewParameters['widgetData'] = (is_callable($this->viewParameters['widgetData'])) ? $this->viewParameters['widgetData']() : $this->dataFunction();
        if (empty($this->viewParameters['widgetData'])) {
            $this->setWidgetContents('<h4 class="c-primary text-center text-capitalize align-middle">No data is available...</h4>');
        }
        return (false=== $this->view?view('fe_widgets::widgetFrame', $this->viewParameters)->render():$this->view->with($this->viewParameters)->render());
    }

    public function renderAjax()
    {
        return response()->json(['target'=>$this->MyID(),'data' => $this->getAjaxData()]);
    }

    public abstract function getAjaxData();

    public abstract function dataFunction();
}

?>