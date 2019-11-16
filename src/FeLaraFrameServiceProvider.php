<?php

namespace feiron\felaraframe;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use feiron\felaraframe\lib\FeFrame;

class FeLaraFrameServiceProvider extends ServiceProvider {

    public function boot(){

        if ($this->app->runningInConsole()) {
            $this->commands([
                commands\fe_BluePrints::class
            ]);
        }

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

        //publish widget assets
        $this->publishes([
            __DIR__ . '/widgets/assets' => public_path('feiron/' . $PackageName. "/widgets/"),
        ], ($PackageName . '_widgets'));
        View::share('siteInfo', [
            'Setting'=> app()->FeFrame->GetSiteSettings(),
            'themeSettings'=> (app()->FeFrame->GetThemeSettings() ?? []),
            'theme'=>(((app()->FeFrame->GetCurrentTheme())->name())?? 'felaraframe')
        ]);

        app()->frameOutlet->bindOutlet('Fe_FrameOutlet', new \feiron\felaraframe\lib\outlet\feOutlet([
            'view' => 'felaraframe::ThemeManagement',
            'myName' => 'Theme Management',
            'reousrce' => [
                asset('/feiron/felaraframe/js/sidebar_hover.js'),
                asset('/feiron/felaraframe/js/ThemeManagement.js')
            ]
        ]));
    }

    public function register(){
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('menuGenerator', '\feiron\felaraframe\lib\facades\menuGenerator');
        $this->app->register( '\feiron\felaraframe\widgets\Fe_WidgetServiceProvider');
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
        Blade::component('felaraframe::components.sidebarMenu', 'fesidebarMenu');
        Blade::component('felaraframe::components.Notes', 'fenotes');
        Blade::component('felaraframe::components.FileUpload', 'fefileupload');
        Blade::component('felaraframe::components.Modal', 'feModal');
        Blade::component('felaraframe::components.DataTable', 'feDataTable');
        Blade::component('felaraframe::components.form.DatePicker', 'feDatePicker');
        Blade::component('felaraframe::components.layout.Portlet', 'fePortlet');

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