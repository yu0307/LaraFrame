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
        $defaultParameters['tablecontents'] = '';
        parent::__construct(array_merge($defaultParameters, ($viewParameters ?? [])));
    }

    public function buildContents(){
        $this->viewParameters['tablecontents']=(
                                                    is_callable($this->viewParameters['tablecontents'])? 
                                                    $this->viewParameters['tablecontents']()
                                                    : 
                                                    $this->viewParameters['tablecontents']
                                                );
    }

    public function render(){
        return view('fe_widgets::widgetTable',$this->viewParameters)->render();
    }
}

?>