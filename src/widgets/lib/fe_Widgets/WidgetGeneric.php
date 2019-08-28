<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetGeneric extends Widget{

    public function __construct($viewParameters)
    {
        parent::__construct(($viewParameters ?? []));
        $this->setView('fe_widgets::widgetFrame');
        if (false === empty($this->viewParameters['widgetData'])) {
            $this->viewParameters['Widget_contents'] = (is_callable($this->viewParameters['widgetData'])) ? $this->viewParameters['widgetData']() : $this->viewParameters['widgetData'];
        }
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
?>