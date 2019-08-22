<?php

namespace feiron\felaraframe\widgets;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

class WidgetProvider {
    private $app;
    private $widgetMaps=[];

    public function __construct(\Illuminate\Foundation\Application $app=null)
    {
        $this->app = $app;
    }

    public function bind($abstract){
        if(!class_exists($abstract)){//if $abstract is not a class and CANNOT be resolved.
            //get resolved class name from service container
            //Throws fatal error if no class can be resolved. I don't think a soft error message or exception is needed. 
            $abstract= get_class(resolve($abstract));
        }
        $pos = strrpos($abstract, '\\');
        $className= false === $pos ? $abstract : substr($abstract, $pos + 1);
        $this->widgetMaps[$className] = $abstract;
    }

    public function BuildWidget($widgetName,$viewParameters=null): Widget{
        return new $this->widgetMaps[$widgetName]($viewParameters);
    }

    public function renderWidget($widgetName, $viewParameters = null){
        return (new $this->widgetMaps[$widgetName]($viewParameters))->render();
    }
}
?>