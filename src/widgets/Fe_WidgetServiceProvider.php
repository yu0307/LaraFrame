<?php

namespace feiron\felaraframe\widgets;

use Illuminate\Support\ServiceProvider;
use feiron\felaraframe\widgets\WidgetProvider;
use feiron\felaraframe\widgets\WidgetManager;
use Illuminate\Support\Facades\Blade;

class Fe_WidgetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Widget', function ($app) {
            return new WidgetProvider($app);
        });

        $this->app->singleton('WidgetManager', function ($app) {
            return new WidgetManager($app);
        });
    }

    public function boot()
    {
        //locading widgets route files
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        //location widgets view files
        $this->loadViewsFrom(__DIR__ . '/view', 'fe_widgets');

        Blade::directive('Widgets', function ($widgetExpression) {
            return "<?=(app()->Widget->BuildWidget($widgetExpression)->render())?>";
        });
    }
}
