<?php

namespace FeIron\LaraFrame;

use Illuminate\Support\ServiceProvider;

class LaraFrameServiceProvider extends ServiceProvider {
    public function boot(){
        $PackageName='LaraFrame';
        //locading package route files
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        //location package view files
        $this->loadViewsFrom(__DIR__ . '/resources/views', $PackageName);
        //loading migration scripts
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/config' => config_path($PackageName),
        ], ($PackageName . '_config'));
        //set the publishing target path for asset files. Run only during update and installation of the package. see composer.json of the package.
        $this->publishes([
            __DIR__ . '/assets' => public_path('FeIron/' . $PackageName),
        ], ($PackageName . '_public'));

        $this->publishes([
            __DIR__ . '/assets/js' => public_path('FeIron/' . $PackageName.'/js'),
            __DIR__ . '/assets/css' => public_path('FeIron/' . $PackageName . '/css')
        ], ($PackageName . '_public_scripts'));
    }

    public function register(){

    }
}

?>