<?php
    Route::group(['namespace' => 'feiron\felaraframe\widgets\controller', 'middleware' => ['web']], function () {        
        Route::post('WidgetsAjax/{tarWidget}/{tarControl}', function($tarWidget, $tarControl){
            return app()->Widget->BuildWidget($tarWidget)->SetID($tarControl)->renderAjax($tarControl);
        })->name('WidgetsAjaxPost');

        Route::get('WidgetsAjax/{tarWidget}/{tarControl}', function($tarWidget, $tarControl){
            return app()->Widget->BuildWidget($tarWidget)->SetID($tarControl)->renderAjax($tarControl);
        })->name('WidgetsAjaxGet');

        Route::post('GetWidget/{WidgetName}', function ($WidgetName) {
            return response()->json(app()->WidgetManager->renderUserWidget($WidgetName,true));
        })->name('WidgetsAjaxBuild');
    });
?>