<?php
    Route::group(['namespace' => 'feiron\felaraframe\http\controllers', 'middleware' => ['web']], function () {

        Route::post('savenote', 'fe_notesController@saveNotes')->name('LF_SaveNotes');
        Route::get('getNotesFilterSelect/{column}', 'fe_notesController@getNotesSelect')->name('LF_GetNotesSelect');

        Route::get('getUsersByRole/{roleName}','LF_Role_Controller@getUsersByRole')->name('getUserByRoles');
        Route::post('lf_uploadfiles','LF_FileUpload_Controller@processUploads')->name('LF_FileUploads');

        Route::group(['middleware' => ['auth']], function () {
            Route::get('controlpanel','fe_controlpanel@show')->name('LF_controlpanel');
        });
    });

    Route::group(['namespace' => 'feiron\felaraframe\http\controllers'], function () {
        Route::get('MyPrivacy',function(){
            return view('felaraframe::terms');
        })->name('PrivacyPolicy');
    }); 
?>