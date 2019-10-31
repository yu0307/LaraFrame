<?php

namespace feiron\felaraframe;

use Illuminate\Support\ServiceProvider;
use feiron\felaraframe\lib\helper\FeFrameHelper;

class FeFrameProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('FeFrame', function ($app) {
            return new FeFrameHelper();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    { }
}
