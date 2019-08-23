<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetGeneric extends Widget{

    public function __construct()
    {
        
    }

    public function render(){
        return view('fe_widgets::widgetFrame');
    }

    public function buildContents(){
        
    }
}

?>