<?php

namespace feiron\felaraframe\lib\helper;
use feiron\felaraframe\lib\feMenuItem;

class menuGenerator {
    private $menu;

    public function __construct()
    {
        $this->menu=['default'=>[]];
    }

    public function addMenu($menuItem,$location='default'){
        array_push($this->menu[$location],new feMenuItem($menuItem));
        return $this->menu[$location][count($this->menu[$location])-1];
    }

    public function addMenus($menuItems,$location='default'){
        foreach($menuItems as $menu){
            $this->addMenu($menu,$location);
        }
        return $this;
    }

    public function getMenu($location='default'){
        if(array_key_exists($location,$this->menu)){
            $output = [];
            foreach($this->menu[$location] as $menu){
                $output=array_merge($output,[$menu->outputMenu()]);
            }
            return $output;
        }
        return [];
    }
}

?>