<?php
    Route::group(['namespace' => 'feiron\felaraframe\http\controllers', 'middleware' => ['web','auth']], function () {

        Route::get('/', function () { return view('felaraframe::dashboard'); });

        if(!Route::has('home')){
            Route::get('home', function () { return view('felaraframe::dashboard'); })->name('home');
        }        
        
        Route::post('savenote', 'fe_notesController@saveNotes')->name('LF_SaveNotes');
        Route::get('getNotesFilterSelect/{column}', 'fe_notesController@getNotesSelect')->name('LF_GetNotesSelect');

        Route::get('getUsersByRole/{roleName}','LF_Role_Controller@getUsersByRole')->name('getUserByRoles');
        Route::post('lf_uploadfiles','LF_FileUpload_Controller@processUploads')->name('LF_FileUploads');

        Route::group(['middleware' => ['auth']], function () {
            Route::get('controlpanel','fe_controlpanel@show')->name('LF_controlpanel');
            Route::post('controlpanel','fe_controlpanel@SaveSettings');
            Route::get('profile/{uid?}','fe_profileController@show')->name('Profile');
            Route::get('notifications/{MID?}','fe_NotificationController@show')->name('LF_Notifications');
            Route::post('notifications/{MID}','fe_NotificationController@loadNotification')->where('MID', '[0-9]+');
            Route::post('notifications/remove/{MID}', 'fe_NotificationController@removeNotification')->where('MID', '[0-9]+');
            Route::post('thememanagement', 'fe_controlpanel@UpdateTheme')->name('updateThemeSetting');
            Route::get('thememanagement/load/{ThemeName}', 'fe_controlpanel@LoadThemeInfo')->name('LoadThemeInfo');
        });
    });

    Route::group(['namespace' => 'feiron\felaraframe\http\controllers'], function () {
        Route::get('MyPrivacy',function(){ return view('felaraframe::terms'); })->name('PrivacyPolicy');
    }); 
    
?>