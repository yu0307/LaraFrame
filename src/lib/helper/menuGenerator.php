<?php

namespace feiron\felaraframe\lib\helper;
use Illuminate\Support\Facades\Route;
class menuGenerator {
    private $menu=[];
    public static function getMenuFromRoutes(){
        if(config('felaraframe.appconfig.use_route_as_menu')){
            return array_merge(
                (Route::has('home')?[['title'=>'home','href'=>route('home')]]:[]), 
                array_map(
                        function ($val) {
                            return ['title' =>explode('.', $val)[1], 'href'=>route($val)];
                        },
                        preg_grep(
                            '/^FrameMenus.([\w|\S| ]*)$/i',
                            array_keys(Route::getRoutes()->getRoutesByName())
                        )
                    )
                );
        }
        return [];
    }
    public function addMenu($menuItem){
        if(array_key_exists('title',$menuItem)){
            array_push($this->menu,[
                'title'=>$menuItem['title'],
                'href'=>$menuItem['href'],
                'icon'=>($menuItem['icon']??''),
                'class'=>($menuItem['class']??'')
            ]);
        }else{
            foreach($menuItem as $menu){
                array_push($this->menu,[
                    'title'=>$menu['title'],
                    'href'=>$menu['href'],
                    'icon'=>($menu['icon']??''),
                    'class'=>($menu['class']??'')
                ]);
            }
        }
    }
    public function getMenu(){
        return $this->menu??[];
    }
}

?>