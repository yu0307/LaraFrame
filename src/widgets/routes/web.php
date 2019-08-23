<?php
    Route::group(['namespace' => 'feiron\felaraframe\widgets\controller', 'middleware' => ['web']], function () {        
        Route::get('testWidgets', function(){
            $this->app->Widget->bind(feiron\felaraframe\widgets\lib\fe_Widgets\WidgetTable::class);
            
            dd($this->app->Widget->BuildWidget('WidgetTable',['headers'=>['a', 'b', 'c', 'd']])->render());  
        })->name('testWidgets');
    });
?>