<?php
    Route::group(['namespace' => 'FeIron\LaraFrame\http\controllers', 'middleware' => ['web']], function () {

        Route::post('savenote', 'fe_notesController@saveNotes')->name('LF_SaveNotes');
        Route::get('getNotesFilterSelect/{column}', 'fe_notesController@getNotesSelect')->name('LF_GetNotesSelect');

        Route::get('getUsersByRole/{roleName}','LF_Role_Controller@getUsersByRole')->name('getUserByRoles');
        Route::post('lf_uploadfiles','LF_FileUpload_Controller@processUploads')->name('LF_FileUploads');
        
        Route::get('testframe', function(){     
            // // dump($this->app);  
            // // dd(Route::has('home')?'Y':"N");
            // dd(menuGenerator::getMenuFromRoutes());
            // dd(preg_grep('/^FrameMenus([\w|\S]*)$/i', array_keys(Route::getRoutes()->getRoutesByName())));
        })->name('testframes');
    });
?>