<?php

namespace feiron\felaraframe;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use feiron\felaraframe\lib\outlet\frameOutlet;
use Illuminate\Support\Facades\Blade;

class FrameOutletProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('frameOutlet', function ($app) {
            return new frameOutlet();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('FrameOutlet', function ($params) {
            list($Manager, $section)=explode(',', $params);
            return
                '
                <?php 
                        $__env->startSection(' . $section . '); 
                        foreach(('. $Manager. '->OutletRenders('. $section . ')) as $view){
                            if ($__env->exists($view->Name(),$view->getData())){
                                 echo $__env->make($view->Name(),$view->getData(), \Illuminate\Support\Arr::except(get_defined_vars(), ["__data", "__path"]))->render(); 
                            }
                        }
                        $__env->stopSection();

                        $__env->startPush(' . $section . ');
                        foreach((' . $Manager . '->OutletResources(' . $section . ')) as $res){
                            echo $res;
                        }
                        $__env->stopPush(); 
                ?>
                ';
        });
    }

    public function provides()
    {
        return [frameOutlet::class];
    }
}
