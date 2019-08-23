<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;
    protected $view=false;
    protected $dataFunction=null;

    public function __construct($viewParameters)
    {
        $this->viewParameters['HeaderBackground'] = 'bg-primary';
        $this->viewParameters['HeaderIcon'] = 'star';
        $this->viewParameters['Width'] = '4';
        $this->viewParameters['Widget_contents'] = '';
        $this->viewParameters['widgetData']='';
        $this->viewParameters['AjaxLoad'] = false;
        $this->viewParameters = array_merge($this->viewParameters, ($viewParameters ?? []));
    }

    protected function setWidgetContents($content){
        $this->viewParameters['Widget_contents'] = $content;
    }

    protected function setView($viewName){
        $this->view=view($viewName);
    }

    //render final widget html to the pipeline
    public function render(){
        $this->buildContents($this->dataFunction);
        return (false=== $this->view?view('fe_widgets::widgetFrame', $this->viewParameters)->render():$this->view->with($this->viewParameters)->render());
    }

    //build contents into the frame
    protected function buildContents(callable $dataFunction=null){
        if(isset($dataFunction)){
            $this->viewParameters['widgetData'] = $dataFunction();
        }else{
            $this->viewParameters['widgetData'] = (is_callable($this->viewParameters['widgetData']) ? $this->viewParameters['widgetData']() : $this->viewParameters['widgetData']);
        }
        
        if (empty($this->viewParameters['widgetData'])) {
            $this->setWidgetContents('<h4 class="c-primary text-center text-capitalize align-middle">No data is available...</h4>');
        }
    }
}

?>