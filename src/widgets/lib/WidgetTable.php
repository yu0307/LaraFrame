<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetTable extends Widget{

    /*
        $viewParameters: 
        Extends @parent:$viewParameters
        Widget specific vars:
        $viewParameters['headers'] : table headers
    */
    public function __construct($viewParameters)
    {
        parent::__construct();
        $this->viewParameters= $viewParameters;
    }

    public function render(){
        return view('fe_widgets::widgetTable',$this->viewParameters)->render();
    }
}

?>