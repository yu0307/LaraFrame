<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetGeneric extends Widget{

    public function __construct($viewParameters)
    {
        parent::__construct(($viewParameters ?? []));
        $this->setView('fe_widgets::widgetFrame');
        if (false === empty($this->viewParameters['WidgetData'])) {
            $this->viewParameters['Widget_contents'] = (is_callable($this->viewParameters['WidgetData'])) ? $this->viewParameters['WidgetData']() : $this->viewParameters['WidgetData'];
        }
    }

    public function dataFunction()
    {
        return $this->viewParameters['Widget_contents'];
    }
}
?>