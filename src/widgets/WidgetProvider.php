<?php

namespace feiron\felaraframe\widgets;

use feiron\felaraframe\widgets\contracts\feWidgetContract as Widget;

class WidgetProvider {
    private $app;
    private $widgetMaps=[];

    public function __construct(\Illuminate\Foundation\Application $app)
    {
        $this->app = $app;
        $this->initBuiltinWidgets();
    }

    private function initBuiltinWidgets(){
        //we could use readdir to scan through the widget dir. But I don't like execution involving with I/O
        $baseWidgets=[
            lib\fe_Widgets\WidgetGeneric::class,
            lib\fe_Widgets\WidgetTable::class
        ];
        foreach($baseWidgets as $widget){
            $this->bind($widget);
        }
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
        $widgetName=trim($widgetName,"'");
        return (new $this->widgetMaps[$widgetName]($viewParameters));
    }
}
?>