<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

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
        //Widget Defaults 
        $defaultParameters['Width'] = '5';
        $defaultParameters['HeaderIcon'] = 'th-list';
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
        $this->setView('fe_widgets::widgetTable');
    }
}

?>