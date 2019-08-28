<?php
    Route::group(['namespace' => 'feiron\felaraframe\widgets\controller', 'middleware' => ['web']], function () {        
        Route::post('WidgetsAjax/{tarWidget}/{tarControl}', function($tarWidget, $tarControl){
            return app()->Widget->BuildWidget($tarWidget)->SetID($tarControl)->renderAjax($tarControl);
        })->name('WidgetsAjaxPost');

        Route::get('WidgetsAjax/{tarWidget}/{tarControl}', function($tarWidget, $tarControl){
            return app()->Widget->BuildWidget($tarWidget)->SetID($tarControl)->renderAjax($tarControl);
        })->name('WidgetsAjaxGet');
    });
?>