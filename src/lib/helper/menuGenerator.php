<?php

namespace FeIron\LaraFrame\lib\helper;
use Illuminate\Support\Facades\Route;
class menuGenerator {
    public static function getMenuFromRoutes(){
        if(config('LaraFrame.appconfig.use_route_as_menu')){
            return array_merge(
                (Route::has('home')?[['title'=>'home','href'=>route('home')]]:[]), 
                array_map(
                        function ($val) {
                            return ['title' =>explode('.', $val)[1], 'href'=>route($val)];
                        },
                        preg_grep(
                            '/^FrameMenus([\w|\S]*)$/i',
                            array_keys(Route::getRoutes()->getRoutesByName())
                        )
                    )
                );
        }
        return [];
    }
}

?>