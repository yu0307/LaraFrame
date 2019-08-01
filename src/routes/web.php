<?php
    Route::group(['namespace' => 'FeIron\LaraFrame\http\controllers', 'middleware' => ['web'], 'as' => 'FrameMenus'], function () {
        Route::get('home','fe_homeController@home')->name('home');
        Route::get('testframe', function(){
            dump(array_keys(Route::getRoutes()->getRoutesByName()));
            
            dd(array_map(function($val){return str_replace('FrameMenus','',$val);}, preg_grep('/^FrameMenus([\w|\S]*)$/i', array_keys(Route::getRoutes()->getRoutesByName()))));
        })->name('testframe');
    });

    Route::group(['as'=>'FrameMenus'],function(){
        Route::get('m1', 'fe_homeController@home')->name('Menu--1');
        Route::get('m2', 'fe_homeController@home')->name('Menu--2');
        Route::get('m3', 'fe_homeController@home')->name('Menu--3');
        Route::get('m4', 'fe_homeController@home')->name('Menu--4');
        Route::get('m5', 'fe_homeController@home')->name('Menu--5');
    });
?>