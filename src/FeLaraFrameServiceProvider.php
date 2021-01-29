<?php

namespace feiron\felaraframe;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use feiron\felaraframe\lib\FeFrame;

class FeLaraFrameServiceProvider extends ServiceProvider {

    public function boot(){

        $PackageName='felaraframe';
        //locading package route files
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        //loading route files for blueprints defined route if any
        if(file_exists(base_path('routes/BluePrints/').'BluePrintsRoute.php')){
            $this->loadRoutesFrom(base_path('routes/BluePrints/') . 'BluePrintsRoute.php');
        }
        

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

        View::share('siteInfo',
                                array_merge((View::getShared('siteInfo')??[]),[
                                    'Setting'=> app()->FeFrame->GetSiteSettings()
                                ])
                    );
    }

    public function register(){
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('menuGenerator', '\feiron\felaraframe\lib\facades\menuGenerator');
        $this->app->register( '\feiron\felaraframe\FrameOutletProvider');

        $this->app->singleton('FeFrame', function ($app) {
            return new FeFrame();
        });
        
        resolve('frameOutlet')
        ->registerOutlet('Fe_FrameOutlet')
        ->registerOutlet('Fe_FrameProfileOutlet');
        
    }

    private function registerBladeComponents(){
        //read from dir and build a cache and load from cache.
        // Blade::component('fe-sidebar-menu', \feiron\felaraframe\lib\components\feSidebarMenu::class);
        // Blade::component('fe-notes', \feiron\felaraframe\lib\components\feNotes::class);
        // Blade::component('fe-file-upload', \feiron\felaraframe\lib\components\feFileUpload::class);
        // Blade::component('fe-modal', \feiron\felaraframe\lib\components\feModal::class);
        // Blade::component('fe-portlet', \feiron\felaraframe\lib\components\fePortlet::class);
        // Blade::component('fe-date-picker', \feiron\felaraframe\lib\components\feDatePicker::class);
        // Blade::component('fe-data-table', \feiron\felaraframe\lib\components\feDataTable::class);

        Blade::directive('pushonce', function ($expression) {
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