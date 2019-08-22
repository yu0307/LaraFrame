<?php

namespace feiron\felaraframe\widgets;

use Illuminate\Support\ServiceProvider;
use feiron\felaraframe\widgets\WidgetProvider;
class Fe_WidgetServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Widget', function ($app) {
            return new WidgetProvider($app);
        });
    }

    public function boot()
    {
        //locading widgets route files
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        //location widgets view files
        $this->loadViewsFrom(__DIR__ . '/view', 'fe_widgets');

    }
}
