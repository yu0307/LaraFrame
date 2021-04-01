<?php
namespace feiron\felaraframe\lib;

use feiron\felaraframe\lib\contracts\feMenuItemInterface;

class feMenuItem implements feMenuItemInterface {

    private $menus=[];
    private $menuInfo;
    public function __construct($menuDetail)
    {
        $this->menuInfo=array_merge([
            'title'=>'Menu',
            'href'=>'#',
            'icon'=>"",
            'class'=>""
        ],$menuDetail);
        return $this;
    }

    public function addMenu($menuDetail){
        $menu= new feMenuItem($menuDetail);
        array_push($this->menus,$menu);
        return $menu;
    }

    public function outputMenu():array{
        $subMenus=[];
        foreach($this->menus as $menu){
            array_push($subMenus,$menu->outputMenu());
        }
        return array_merge($this->menuInfo,['subMenu'=>$subMenus]);
    }
}