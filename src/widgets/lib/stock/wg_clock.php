<?php

namespace feiron\felaraframe\widgets\lib\stock;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class wg_clock extends Widget{

    /*
        $viewParameters: 
        Extends @parent:$viewParameters
        Widget specific vars: none
    */
    public function __construct($viewParameters){
        //Widget Defaults 
        $defaultParameters=[
            'WidgetName'=>'clock',
            'Width'=>'2',
            'DataHeight'=>280,
            'HeaderBackground'=> 'bg-transparent',
            'WidgetBackground'=> 'bg-transparent',
            'HeaderIcon'=>false,
            'DisableDigital'=>false
        ];
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
        $this->setView('fe_widgets::stock.wg_clock');
    }

    public function dataFunction()
    {
        return $this->viewParameters['Widget_contents'];
    }

    public function getAjaxData()
    {
        return $this->dataFunction();
    }
}
