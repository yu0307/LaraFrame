<?php

namespace feiron\felaraframe\widgets\lib\fe_Widgets;

use feiron\felaraframe\widgets\lib\WidgetAbstract as Widget;

class WidgetGeneric extends Widget{

    public function __construct($viewParameters)
    {
        parent::__construct(($viewParameters ?? []));
        $this->setView('fe_widgets::widgetFrame');
    }
}

?>