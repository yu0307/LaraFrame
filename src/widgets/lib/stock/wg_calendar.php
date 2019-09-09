<?php

namespace feiron\felaraframe\widgets\lib\stock;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class wg_calendar extends Widget
{

    /*
        $viewParameters: 
        Extends @parent:$viewParameters
        Widget specific vars: none
    */
    public function __construct($viewParameters)
    {
        //Widget Defaults 
        $defaultParameters = [
            'WidgetName' => 'calendar',
            'Width' => '3',
            'DataHeight' => 400,
            'HeaderBackground' => 'bg-transparent',
            'WidgetBackground' => 'bg-transparent',
            'HeaderIcon' => false
        ];
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
        $this->setView('fe_widgets::stock.wg_calendar');
    }

    public function dataFunction()
    {
        return $this->viewParameters['Widget_contents'];
    }
}
