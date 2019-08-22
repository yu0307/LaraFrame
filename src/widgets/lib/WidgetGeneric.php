<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetTable extends Widget{

    public function __construct()
    {
        
    }

    public function render(){
        return view('fe_widgets::widgetFrame');
    }
}

?>