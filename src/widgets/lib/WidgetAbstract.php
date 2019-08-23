<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;

    public function __construct($viewParameters)
    {
        $this->viewParameters['HeaderBackground'] = 'bg-primary';
        $this->viewParameters['HeaderIcon'] = 'star';
        $this->viewParameters['Width'] = '4';
        $this->viewParameters['Widget_contents'] = '';
        $this->viewParameters['AjaxLoad'] = false;
        $this->viewParameters = array_merge($this->viewParameters, ($viewParameters ?? []));

    }

    abstract public function render();

    abstract public function buildContents();
}

?>