<?php

namespace \felaraframe;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;

class felaraframeServiceProvider extends ServiceProvider {

    public function boot(){

        $PackageName='felaraframe';
        //locading package route files
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        //location package view files
        $this->loadViewsFrom(__DIR__ . '/resources/views', $PackageName);
        //loading migration scripts
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->registerBladeComponents();

        $this->publishes([
            __DIR__ . '/config' => config_path($PackageName),
        ], ($PackageName . '_config'));
        //set the publishing target path for asset files. Run only during update and installation of the package. see composer.json of the package.
        $this->publishes([
            __DIR__ . '/assets' => public_path('/' . $PackageName),
        ], ($PackageName . '_public'));

        $this->publishes([
            __DIR__ . '/assets/js' => public_path('/' . $PackageName.'/js'),
            __DIR__ . '/assets/css' => public_path('/' . $PackageName . '/css')
        ], ($PackageName . '_public_scripts'));
        

        // Event::listen('feiron\fe_login\lib\events\UserCreated', '\felaraframe\lib\Listeners\UserCreated');
    }

    public function register(){
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('menuGenerator', '\felaraframe\lib\facades\menuGenerator');
    }

    private function registerBladeComponents(){
        //read from dir and build a cache and load from cache.
        Blade::component('felaraframe::components.sidebarMenu', 'fesidebarMenu');
        Blade::component('felaraframe::components.Notes', 'fenotes');
        Blade::component('felaraframe::components.FileUpload', 'fefileupload');
    }
}

?>