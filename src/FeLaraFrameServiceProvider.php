<?php

namespace feiron\felaraframe;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;

class FeLaraFrameServiceProvider extends ServiceProvider {

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
            __DIR__ . '/assets' => public_path('feiron/' . $PackageName),
        ], ($PackageName . '_public'));

        $this->publishes([
            __DIR__ . '/assets/js' => public_path('feiron/' . $PackageName.'/js'),
            __DIR__ . '/assets/css' => public_path('feiron/' . $PackageName . '/css')
        ], ($PackageName . '_public_scripts'));

        //publish widget assets
        $this->publishes([
            __DIR__ . '/widgets/assets' => public_path('feiron/' . $PackageName. "/widgets/"),
        ], ($PackageName . '_widgets'));
        

        // Event::listen('feiron\fe_login\lib\events\UserCreated', '\felaraframe\lib\Listeners\UserCreated');
    }

    public function register(){
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('menuGenerator', '\feiron\felaraframe\lib\facades\menuGenerator');
        $this->app->register( '\feiron\felaraframe\widgets\Fe_WidgetServiceProvider');
    }

    private function registerBladeComponents(){
        //read from dir and build a cache and load from cache.
        Blade::component('felaraframe::components.sidebarMenu', 'fesidebarMenu');
        Blade::component('felaraframe::components.Notes', 'fenotes');
        Blade::component('felaraframe::components.FileUpload', 'fefileupload');
        Blade::component('felaraframe::components.Modal', 'feModal');


        Blade::directive('pushonce', function ($expression) {
            // $expression = substr(substr($expression, 0, -1), 1);
            // Split variable and its value
            list($push_name, $push_sub) = explode('\',', $expression, 2);
            $push_name=trim($push_name,"'");
            $isDisplayed = '__pushonce_' . $push_name . '_'."{{$push_sub}}";
            return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush('{$push_name}'); ?>";
        });
        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });
    }
}

?>