<?php

namespace feiron\felaraframe\widgets\lib;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

abstract class WidgetAbstract implements Widget{

    protected $viewParameters;

    public function __construct()
    {
        $this->viewParameters['HeaderBackground'] = 'bg-success';
        $this->viewParameters['HeaderIcon'] = 'star';
    }

    abstract public function render();
}

?>